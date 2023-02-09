<?php

namespace Tutorial\Http\Utils;

use GuzzleHttp\Psr7\Response;

final class HttpResponse
{
    public static function send(Response $response) {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            header($name . ': ' . \implode(', ', $values));
        }

        echo $response->getBody();
    }

    public static function html($filePath, $params = []): Response {
        ob_start();

        include $filePath;

        return new Response(200, [
            'Content-Type' => 'text/html',
        ], ob_get_clean());
    }
}