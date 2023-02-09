<?php

namespace Tutorial\Http\Controllers;

use AmoMessenger\Service;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Tutorial\Repository\AccessTokenRepository;
use Tutorial\Widgets\WidgetFactory;

class WebhookController
{
    protected Service $messenger;
    protected AccessTokenRepository $accessTokenRepository;
    protected LoggerInterface $log;
    protected WidgetFactory $widgetFactory;

    /**
     * @param Service $messenger
     */
    public function __construct(Service $messenger, AccessTokenRepository $accessTokenRepository, WidgetFactory $widgetFactory, LoggerInterface $log)
    {
        $this->messenger = $messenger;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->widgetFactory = $widgetFactory;
        $this->log = $log;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $webhookData = $this->messenger->decodeWebhook($request);
        $this->log->info('webhook income', ['data' => $webhookData, 'signature' => $request->getHeader('X-Signature')]);

        // todo process webhook in background

        switch ($webhookData['event_type']) {
            case 'income_message':
                $this->processIncomeMessage($webhookData);
                break;
            case 'rpa_bot_control_transferred':
            case 'rpa_bot_income_message':
                $this->delegateWebhookToWidget($webhookData);
                break;
        }

        return new Response(200);
    }

    private function processIncomeMessage(array $webhookData): void
    {
        $accessToken = $this->accessTokenRepository->getToken($webhookData['_embedded']['context']['company_id']);
        $this->messenger->sendMessage($accessToken, [
            'conversation_identity' => $webhookData['_embedded']['conversation_identity'],
            'message' => array_merge(
                [
                    'receiver' => $webhookData['_embedded']['message']['author'],
                    'reply_to' => [
                        'conversation_identity' => $webhookData['_embedded']['conversation_identity'],
                        'msg_id' => $webhookData['_embedded']['message']['id'],
                    ]
                ],
                array_intersect_key(
                    $webhookData['_embedded']['message'],  // the array with all keys
                    array_flip(['text', 'attachments']), // keys to be extracted
                ),
            )
        ]);
    }

    private function delegateWebhookToWidget(array $webhookData): void
    {
        $widgetId = $webhookData['_embedded'][$webhookData['event_type']]['widget_id'];
        $this->widgetFactory->build($widgetId)->processWebhook($webhookData);
    }
}