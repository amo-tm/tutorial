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

// Распарсим токен
$accessToken = new \League\OAuth2\Client\Token\AccessToken(
    json_decode($accessTokenJson, true)
);

$message = $parsedBody['_embedded']['message'];
$conversationIdentity = $parsedBody['_embedded']['conversation_identity'];

// Подготовим запрос на ответ
$answerBody = [
    'reply_to' => [
        'conversation_identity' => $conversationIdentity,
        'msg_id' => $message['id']
    ]
];

if ($messageText = $message['text'] ?? null) {
    $answerBody['text'] = $messageText;
}

if ($attachments = $message['attachments'] ?? null) {
    $answerBody['attachments'] = $attachments;
}

// Инициализируем провайдер
$provider = new League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                 => getenv('CLIENT_ID') ?? null,
    'clientSecret'             => getenv('CLIENT_SECRET') ?? null,
    'redirectUri'              => "https://{$_SERVER['SERVER_NAME']}/amo_authorization.php",
    'urlAuthorize'             => 'https://id.amo.tm/access',
    'urlAccessToken'           => 'https://id.amo.tm/oauth2/access_token',
    'urlResourceOwnerDetails'  => null
]);

// Отправим запрос
$answerRequest = $provider->getAuthenticatedRequest(
    'POST',
    "https://api.amo.io/v1.3/direct/{$conversationIdentity['direct_id']}/sendMessage",
    $accessToken,
    [
        'headers' => [
            'content-type' => 'application/json'
        ],
        'body' => json_encode($answerBody)
    ]
);

$client = new \GuzzleHttp\Client();
$response = $client->send($answerRequest);
$responseJson = (string) $response->getBody();

file_put_contents("php://stderr", "Answer response: {$responseJson}\n");