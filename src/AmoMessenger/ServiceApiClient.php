<?php

namespace AmoMessenger;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServiceApiClient implements Service
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $serverBaseUrl;
    protected GenericProvider $provider;

    /**
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(string $clientId, string $clientSecret, string $serverBaseUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->serverBaseUrl = $serverBaseUrl;

        $this->provider = new GenericProvider([
            'clientId'                 => $this->clientId,    // The client ID assigned to you by the provider
            'clientSecret'             => $this->clientSecret,    // The client password assigned to you by the provider
            'redirectUri'              => "{$this->serverBaseUrl}/amo_authorization.php",
            'urlAuthorize'             => 'https://id.amo.tm/access',
            'urlAccessToken'           => 'https://id.amo.tm/oauth2/access_token',
            'urlResourceOwnerDetails'  => 'https://api.amo.io/v1.3/me'
        ]);
    }

    public static function fromGlobals() {
        return new self(
            getenv('AMO_CLIENT_ID'),
            getenv('AMO_CLIENT_SECRET'),
            'https://' . $_SERVER['HTTP_HOST'],
        );
    }

    public function exchangeCode(string $code): AccessToken
    {
        return $this->provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);
    }

    public function resourceOwner(AccessToken $accessToken): array
    {
        $request = $this->provider->getAuthenticatedRequest(
            'GET',
            'https://id.amo.tm/oauth2/validate',
            $accessToken,
            [
                'headers' => [
                    'Accept' => 'application/json'
                ],
            ],
        );

        return $this->call($request);
    }

    public function sendMessage(AccessToken $accessToken, array $messageParams): array
    {
        $request = $this->provider->getAuthenticatedRequest(
            'POST',
            'https://api.amo.tm/v1.3/messages',
            $accessToken,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'body' => json_encode($messageParams)
            ],
        );

        return $this->call($request);
    }

    public function decodeWebhook(ServerRequestInterface $request): array
    {
        $webhookBody = (string)$request->getBody();
        $signHeader = $request->getHeader('X-Signature');
        list($algo, $hashSum) = explode('=', reset($signHeader));
        if ($hashSum != hash_hmac($algo, $webhookBody, $this->clientSecret)) {
            throw new \Exception("invalid signature");
        }

        return json_decode($webhookBody, true);
    }

    public function decodeSheetsRequest(ServerRequestInterface $request): array
    {
        return $request->getParsedBody();
    }

    protected function call(RequestInterface $request): array {
        $client = new \GuzzleHttp\Client();
        $response = $client->send($request);
        return json_decode($response->getBody(), true);
    }
}