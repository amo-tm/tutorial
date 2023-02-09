<?php
/*
 * Copyright (c) 2021. AMO | Корпоративный мессенджер.
 * Это  приложение - демо для документации по API.
 * Вы можете использовать данный код в своих проектах без сохранения этого копирайта.
 *
 * @Author Mike Eremin <meremin@team.amocrm.com>
 */

use AmoMessenger\ServiceApiClient;
use GuzzleHttp\Psr7\ServerRequest;
use Tutorial\Config\Config;
use Tutorial\Http\Controllers\AuthorizationController;
use Tutorial\Http\Controllers\WebhookController;
use Tutorial\Http\Utils\Logger;
use Tutorial\Repository\AccessTokenFileRepository;
use Tutorial\Widgets\WidgetFactory;

chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';
//
$logger = new Logger();
$accessTokenRepository = new AccessTokenFileRepository('./store');
$messengerService = ServiceApiClient::fromGlobals();
$controller = new WebhookController(
    $messengerService,
    $accessTokenRepository,
    new WidgetFactory($accessTokenRepository, $messengerService, Config::fromGlobals(), $logger),
    new Logger(),
);

$response = $controller(ServerRequest::fromGlobals());

\Tutorial\Http\Utils\HttpResponse::send($response);