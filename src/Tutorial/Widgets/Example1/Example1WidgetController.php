<?php

namespace Tutorial\Widgets\Example1;

use AmoMessenger\Service;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Tutorial\Http\Utils\HttpResponse;
use Tutorial\Repository\AccessTokenRepository;
use Tutorial\Widgets\WidgetController;

class Example1WidgetController implements WidgetController
{
    protected LoggerInterface $logger;
    protected Service $messenger;
    protected AccessTokenRepository $accessTokenRepository;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(AccessTokenRepository $accessTokenRepository, Service $messenger, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->messenger = $messenger;
        $this->accessTokenRepository = $accessTokenRepository;
    }

    public function render(array $sheetsParams): ResponseInterface
    {
        return HttpResponse::html(
            realpath(__DIR__) . DIRECTORY_SEPARATOR . 'views/sheet.php',
            $sheetsParams,
        );
    }

    public function processWebhook(array $webhookData): void
    {
        $this->logger->info('Widget handle webhook', ['data' => $webhookData]);
        $companyId = $webhookData['_embedded']['context']['company_id'];
        $accessToken = $this->accessTokenRepository->getToken($companyId);

        switch ($webhookData['event_type']) {
            case 'rpa_bot_control_transferred':
                $this->processControlTransferredEvent($accessToken, $webhookData);
                break;
            case 'rpa_bot_income_message':
                $this->processUserMessage($accessToken, $webhookData);
                break;
        }
    }

    private function processControlTransferredEvent(AccessToken $accessToken, array $webhookData)
    {
        $this->sendMessage(
            $accessToken,
            $webhookData['_embedded']['rpa_bot_control_transferred']['bot_id'],
            $webhookData['_embedded']['rpa_bot_control_transferred']['_embedded']['request']['id'],
            [
                'text' => 'Settings fields ID is: ' . $webhookData['_embedded']['rpa_bot_control_transferred']['input_values']['list_example_selected_value'],
                'receiver' => $webhookData['_embedded']['rpa_bot_control_transferred']['_embedded']['request']['responsible'],
            ]
        );
    }

    private function processUserMessage(AccessToken $accessToken, array $webhookData)
    {
        $message = $webhookData['_embedded']['rpa_bot_income_message']['_embedded']['income_message'];
        switch ($message['text'] ?? '') {
            case '/users':
                $this->processUsersCommand(
                    $accessToken,
                    $webhookData['_embedded']['rpa_bot_income_message']['bot_id'],
                    $webhookData['_embedded']['rpa_bot_income_message']['_embedded']['request']['id'],
                    $webhookData['_embedded']['rpa_bot_income_message']['_embedded']['income_message']['author'],
                );
                break;
            case '/success':
                $this->processReturnControlCommand(
                    $accessToken,
                    $webhookData['_embedded']['rpa_bot_income_message']['bot_id'],
                    $webhookData['_embedded']['rpa_bot_income_message']['_embedded']['request']['id'],
                    'success',
                );
                break;
            case '/error':
                $this->processReturnControlCommand(
                    $accessToken,
                    $webhookData['_embedded']['rpa_bot_income_message']['bot_id'],
                    $webhookData['_embedded']['rpa_bot_income_message']['_embedded']['request']['id'],
                    'error',
                );
                break;
        }
    }

    private function processUsersCommand(AccessToken $accessToken, string $botId, string $requestId, array $receiver)
    {
        $users = $this->messenger->users($accessToken);
        $this->sendMessage(
            $accessToken,
            $botId,
            $requestId,
            [
                'text' => 'Your team users is ' . var_export($users['_embedded']['items'], true),
                'receiver' => $receiver,
            ]
        );
    }

    private function sendMessage(AccessToken $accessToken, string $botId, string $requestId, array $messageParams): void
    {
        $this->messenger->rpaRequestSendMessage(
            $accessToken,
            $botId,
            $requestId,
            array_merge($messageParams, ['reply_markup' => [
                'inline_keyboard' => [
                    'buttons' => [
                        ['text' => '/users'],
                        ['text' => '/success'],
                        ['text' => '/error'],
                    ]
                ],
            ],
            ]),
        );
    }

    private function processReturnControlCommand(AccessToken $accessToken, string $botId, string $requestId, string $returnCode): void
    {
        $this->messenger->rpaRequestReturnControl($accessToken, $botId, $requestId, $returnCode);
    }
}