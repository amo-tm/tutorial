<?php

namespace Tutorial\Repository;

use League\OAuth2\Client\Token\AccessToken;

class AccessTokenFileRepository implements AccessTokenRepository
{
    protected string $basePath;

    /**
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = realpath($basePath);
    }

    public function saveToken(string $companyId, AccessToken $accessToken)
    {
        file_put_contents($this->filePath($companyId), json_encode($accessToken->jsonSerialize()));
    }

    public function getToken(string $companyId): AccessToken
    {
        return new AccessToken(json_decode(file_get_contents($this->filePath($companyId)), true));
    }

    private function filePath(string $companyId): string {
        return $this->basePath . DIRECTORY_SEPARATOR . $companyId . '.txt';
    }
}