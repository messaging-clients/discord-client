<?php

namespace EasyHttp\DiscordClient;

use EasyHttp\Contracts\Contracts\EasyClientContract;
use EasyHttp\Contracts\Contracts\HttpClientRequest;
use EasyHttp\Contracts\Contracts\HttpClientResponse;
use EasyHttp\DiscordClient\Constants\AuthorizationType;
use EasyHttp\DiscordClient\Exceptions\MissingAuthorizationTypeException;
use EasyHttp\DiscordClient\Exceptions\MissingClientIdException;
use EasyHttp\DiscordClient\Exceptions\MissingClientSecretException;
use EasyHttp\DiscordClient\Exceptions\MissingTokenException;
use EasyHttp\GuzzleAdapter\GuzzleClient;

class DiscordClient
{
    protected const TOKEN_URL = 'https://discord.com/api/oauth2/token';
    protected string $baseUri = 'https://discord.com/api/v10';
    protected EasyClientContract $client;
    protected Authorization $authorization;

    public function __construct(Authorization $authorization)
    {
        $this->authorization = $authorization;
        $this->client = new GuzzleClient();
    }

    public function withHandler(callable $handler): void
    {
        $this->client->withHandler($handler);
    }

    /**
     * @throws MissingClientIdException
     * @throws MissingClientSecretException
     */
    public function requestAccessToken(array $scopes): HttpClientResponse
    {
        if (!$this->authorization->hasClientId()) {
            throw new MissingClientIdException('Client ID is not set.');
        }

        if (! $this->authorization->hasClientSecret()) {
            throw new MissingClientSecretException('Client Secret is not set.');
        }

        $this->client
            ->prepareRequest('POST', self::TOKEN_URL)
            ->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->client->getRequest()
            ->setUrlEncodedData(
                [
                'grant_type' => 'client_credentials',
                'client_id' => $this->authorization->getClientId(),
                'client_secret' => $this->authorization->getClientSecret(),
                'scope' => implode(' ', $scopes)
                ]
            );

        return $this->client->execute();
    }
    protected function setBearerToken(string $token): HttpClientRequest
    {
        return $this->client->getRequest()
            ->setHeader('Authorization', 'Bearer ' . $token);
    }

    protected function setBotToken(string $token): HttpClientRequest
    {
        return $this->client->getRequest()
            ->setHeader('Authorization', 'Bot ' . $token);
    }

    /**
     * @throws MissingTokenException
     * @throws MissingAuthorizationTypeException
     */
    protected function withAuthorization(): self
    {
        if (!$this->authorization->hasToken()) {
            throw new MissingTokenException('Authorization token is not set.');
        }

        if (!$this->authorization->hasAuthorizationType()) {
            throw new MissingAuthorizationTypeException('Authorization type is not set.');
        }

        switch ($this->authorization->getAuthorizationType()) {
            case AuthorizationType::BEARER_TOKEN:
                $this->setBearerToken($this->authorization->getToken());
                break;
            case AuthorizationType::BOT_TOKEN:
                $this->setBotToken($this->authorization->getToken());
                break;
            default:
                throw new \InvalidArgumentException(
                    'Unsupported auth type: ' . $this->authorization->getAuthorizationType()->value
                );
        }

        return $this;
    }
}
