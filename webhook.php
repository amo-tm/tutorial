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

exit("<pre>{$requestBody}</pre>");
