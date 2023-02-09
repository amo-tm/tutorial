<?php

namespace Tutorial\Repository;

use League\OAuth2\Client\Token\AccessToken;

interface AccessTokenRepository
{
    public function saveToken(string $companyId, AccessToken $accessToken);
    public function getToken(string $companyId): AccessToken;
}