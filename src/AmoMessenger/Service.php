<?php

namespace AmoMessenger;

use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ServerRequestInterface;

interface Service
{
    public function exchangeCode(string $code): AccessToken;
    public function decodeWebhook(ServerRequestInterface $request): array;
    public function resourceOwner(AccessToken $accessToken): array;
    public function sendMessage(AccessToken $accessToken, array $messageParams): array;
    public function decodeSheetsRequest(ServerRequestInterface $request): array;
}