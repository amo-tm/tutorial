<?php

use League\OAuth2\Client\Token\AccessToken;

function storeAddToken(string $companyUuid, AccessToken $accessToken) {
    file_put_contents(storeMakeStorePath($companyUuid), json_encode($accessToken->jsonSerialize()));
}

function storeGetToken(string $companyUuid): ?AccessToken {
    $storePath = storeMakeStorePath($companyUuid);
    return new AccessToken(json_decode(file_get_contents($storePath), true));
}

function storeMakeStorePath(string $companyUuid) {
    return 'store/'.$companyUuid.'.txt';
}

function logInfo(string $msg, array $data = []) {
    file_put_contents("php://stderr", $msg . ": " . var_export($data, true) . "\n");
}