<?php

namespace Tutorial\Widgets;

use AmoMessenger\Service;
use Psr\Log\LoggerInterface;
use Tutorial\Config\Config;
use Tutorial\Repository\AccessTokenRepository;
use Tutorial\Widgets\Example1\Example1WidgetController;

class WidgetFactory
{
    protected Config $config;
    protected Service $messenger;
    protected AccessTokenRepository $accessTokenRepository;
    protected LoggerInterface $logger;

    /**
     * @param Config $config
     */
    public function __construct(
        AccessTokenRepository $accessTokenRepository,
        Service $messenger,
        Config $config,
        LoggerInterface $logger,
    )
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->messenger = $messenger;
        $this->accessTokenRepository = $accessTokenRepository;
    }

    public function build(string $widgetId): WidgetController {
        return match ($widgetId) {
            $this->config->getWidgetExample1Id() => new Example1WidgetController(
                $this->accessTokenRepository,
                $this->messenger,
                $this->logger,
            ),
            default => throw new \Exception('unsupported widget'),
        };
    }
}