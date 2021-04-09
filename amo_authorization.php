<?php
/*
 * Copyright (c) 2021. AMO | Корпоративный мессенджер.
 * Это  приложение - демо для документации по API.
 * Вы можете использовать данный код в своих проектах без сохранения этого копирайта.
 *
 * @Author Mike Eremin <meremin@team.amocrm.com>
 */

use League\OAuth2\Client\Provider\GenericProvider;

require_once 'vendor/autoload.php';

$appURL = "https://{$_SERVER['SERVER_NAME']}";
$clientId = $_ENV['CLIENT_ID'] ?? null;
$clientSecret = $_ENV['CLIENT_SECRET'] ?? null;

$appName = str_replace( '.herokuapp.com', '', $_SERVER['SERVER_NAME']);
$settingsURL = "https://dashboard.heroku.com/apps/{$appName}/settings";

if (!$clientId || !$clientSecret) {
    echo "Пожалуйста, настройте переменные окружения CLIENT_ID и CLIENT_SECRET в настройках приложения на сайте heroku.com. {$settingsURL}";
}

$provider = new GenericProvider([
    'clientId'                 => $clientId,    // The client ID assigned to you by the provider
    'clientSecret'             => $clientSecret,    // The client password assigned to you by the provider
    'redirectUri'              => "{$appURL}/amo_authorization.php",
    'urlAuthorize'             => 'https://id.amo.tm/access',
    'urlAccessToken'           => 'https://id.amo.tm/oauth2/access_token',
    'urlResourceOwnerDetails'  => null
]);

if (!isset($_GET['code'])) {

    exit('Invalid code');

} else {

    try {

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated
        // requests against the service provider's API.
        echo 'Access Token: ' . $accessToken->getToken() . "<br>";
        echo 'Refresh Token: ' . $accessToken->getRefreshToken() . "<br>";
        echo 'Expired in: ' . $accessToken->getExpires() . "<br>";
        echo 'Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";
        echo '<script>setTimeout(function(){window.close()}, 15 * 1000);</script>';

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

    }

}






