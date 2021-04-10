<?php
/*
 * Copyright (c) 2021. AMO | Корпоративный мессенджер.
 * Это  приложение - демо для документации по API.
 * Вы можете использовать данный код в своих проектах без сохранения этого копирайта.
 *
 * @Author Mike Eremin <meremin@team.amocrm.com>
 */

require_once 'vendor/autoload.php';

$requestBody = file_get_contents('php://input');
$parsedBody = json_decode($requestBody, TRUE);

file_put_contents("php://stderr", "{$requestBody}\n");

$redis = new Predis\Client(getenv('REDIS_URL'));
$accessTokenJson = $redis->get("ACCESS_TOKEN");

file_put_contents("php://stderr", "AccessToken: {$accessTokenJson}\n");
