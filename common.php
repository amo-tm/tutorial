<?php

declare(strict_types=1);

use League\OAuth2\Client\Token\AccessToken;

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();
$dotenv->required(['CLIENT_ID', 'CLIENT_SECRET'])->notEmpty();

function setToken(string $teamID, AccessToken $token) {
    set('token_'.$teamID, $token->jsonSerialize());
}

function getToken(string $teamID): ?AccessToken {
    $rawToken = get('token_'.$teamID);
    if (!$rawToken) return null;
    return new AccessToken($rawToken);
}

/**
 * @param string $key
 * @param mixed $value
 * @return void
 */
function set(string $key, $value) {
    $fileName = __DIR__ . '/store/' . $key . '.txt';
    file_put_contents(
        $fileName,
        json_encode($value),
    );
}

/**
 * @param string $key
 * @return mixed|null
 */
function get(string $key) {
    $fileName = __DIR__ . '/store/' . $key . '.txt';
    if (!file_exists($fileName)) {
        return null;
    }

    return json_decode(file_get_contents(
        $fileName,
    ));
}