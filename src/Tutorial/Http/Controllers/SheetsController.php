<?php

namespace Tutorial\Http\Controllers;

use AmoMessenger\Service;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Tutorial\Config\Config;
use Tutorial\Repository\AccessTokenRepository;
use Tutorial\Widgets\Example1\Example1WidgetController;
use Tutorial\Widgets\WidgetController;

class SheetsController
{
    protected Service $messenger;
    protected AccessTokenRepository $accessTokenRepository;
    protected Config $config;
    protected LoggerInterface $log;

    /**
     * @param Service $messenger
     */
    public function __construct(
        Service $messenger,
        AccessTokenRepository $accessTokenRepository,
        Config $config,
        LoggerInterface $log,
    )
    {
        $this->messenger = $messenger;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->config = $config;
        $this->log = $log;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $sheetsParams = $this->messenger->decodeSheetsRequest($request);
        $this->log->info('sheets request', ['data' => $sheetsParams]);

        return $this->widget($sheetsParams['widget_id'])->render($sheetsParams);
    }

    private function widget(string $widgetId): WidgetController
    {
        switch ($widgetId) {
            case $this->config->getWidgetExample1Id():
                return new Example1WidgetController();
            default:
                throw new \Exception('unsupported widget');
        }
    }
}