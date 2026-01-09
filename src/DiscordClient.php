<?php

namespace MessagingClients\DiscordClient;

use EasyHttp\Contracts\Contracts\EasyClientContract;
use EasyHttp\Contracts\Contracts\HttpClientRequest;
use EasyHttp\Contracts\Contracts\HttpClientResponse;
use MessagingClients\DiscordClient\Constants\AuthorizationType;
use MessagingClients\DiscordClient\Exceptions\MissingAuthorizationTypeException;
use MessagingClients\DiscordClient\Exceptions\MissingClientIdException;
use MessagingClients\DiscordClient\Exceptions\MissingClientSecretException;
use MessagingClients\DiscordClient\Exceptions\MissingTokenException;
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

    /**
     * Returns the user object of the requester's account.
     *
     * When used with a bot token, this returns the bot user.
     * When used with a bearer token, this returns the user that authorized the token.
     *
     * @return HttpClientResponse
     * @throws MissingTokenException|MissingAuthorizationTypeException
     */
    public function getCurrentUser(): HttpClientResponse
    {
        $this->client
            ->prepareRequest('GET', $this->baseUri . '/users/@me')
            ->setHeader('Content-Type', 'application/json');

        $this->withAuthorization();

        return $this->client->execute();
    }

    /**
     * Returns all global application commands for your application.
     *
     * @param string $applicationId
     * @return HttpClientResponse
     * @throws MissingAuthorizationTypeException
     * @throws MissingTokenException
     */
    public function getGlobalApplicationCommands(string $applicationId): HttpClientResponse
    {
        $this->client
            ->prepareRequest(
                'GET',
                $this->baseUri . '/applications/' . $applicationId . '/commands'
            )
            ->setHeader('Content-Type', 'application/json');

        $this->withAuthorization();

        return $this->client->execute();
    }

    /**
     * @param string $applicationId
     * @param array $commands
     * @return HttpClientResponse
     * @throws MissingAuthorizationTypeException
     * @throws MissingTokenException
     */
    public function bulkGlobalApplicationCommands(string $applicationId, array $commands): HttpClientResponse
    {
        $this->client
            ->prepareRequest(
                'PUT',
                $this->baseUri . '/applications/' . $applicationId . '/commands'
            )
            ->setHeader('Content-Type', 'application/json');

        $this->withAuthorization();

        $this->client->getRequest()->setJson($commands);

        return $this->client->execute();
    }

    /**
     * Creates a new guild-specific application command.
     *
     * Guild commands are specific to a guild and can be used to create commands
     * that are only available in specific servers.
     *
     * @param string $applicationId The application ID
     * @param string $guildId The guild ID where the command will be created
     * @param array $command The command data structure
     * @return HttpClientResponse
     * @throws MissingAuthorizationTypeException
     * @throws MissingTokenException
     */
    public function createGuildApplicationCommand(string $applicationId, string $guildId, array $command): HttpClientResponse
    {
        $this->client
            ->prepareRequest(
                'POST',
                $this->baseUri . '/applications/' . $applicationId . '/guilds/' . $guildId . '/commands'
            )
            ->setHeader('Content-Type', 'application/json');

        $this->withAuthorization();

        $this->client->getRequest()->setJson($command);

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
