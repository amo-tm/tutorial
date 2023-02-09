<?php

namespace Tutorial\Widgets;

use Psr\Http\Message\ResponseInterface;

interface WidgetController
{
    public function render(array $sheetsParams): ResponseInterface;
}