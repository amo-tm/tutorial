<?php

namespace Tutorial\Http\Controllers;

use AmoMessenger\Service;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tutorial\Repository\AccessTokenRepository;

class AuthorizationController
{
    protected Service $messenger;
    protected AccessTokenRepository $accessTokenRepository;

    /**
     * @param Service $messenger
     */
    public function __construct(Service $messenger, AccessTokenRepository $accessTokenRepository)
    {
        $this->messenger = $messenger;
        $this->accessTokenRepository = $accessTokenRepository;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $accessToken = $this->messenger->exchangeCode($request->getQueryParams()['code']);
        $resourceOwner = $this->messenger->resourceOwner($accessToken);

        $this->accessTokenRepository->saveToken($resourceOwner['company_uuid'], $accessToken);

        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([$accessToken->jsonSerialize(), $resourceOwner]),
        );
    }
}