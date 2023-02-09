<?php

namespace Tutorial\Http\Utils;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        file_put_contents('php://stderr', $message . var_export($context, true));
    }
}