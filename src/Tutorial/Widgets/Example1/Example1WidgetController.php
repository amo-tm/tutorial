<?php

namespace Tutorial\Widgets\Example1;

use Psr\Http\Message\ResponseInterface;
use Tutorial\Http\Utils\HttpResponse;
use Tutorial\Widgets\WidgetController;

class Example1WidgetController implements WidgetController
{
    public function render(array $sheetsParams): ResponseInterface
    {
        return HttpResponse::html(
            realpath(__DIR__) . DIRECTORY_SEPARATOR . 'views/sheet.php',
            $sheetsParams,
        );
    }
}