<?php

namespace MessagingClients\DiscordClient;

use MessagingClients\DiscordClient\Constants\AuthorizationType;

class Authorization
{
    /**
     * client_id for OAuth2 Client Credentials Grant
     *
     * @see https://datatracker.ietf.org/doc/html/rfc6749#section-4.4.2
     *
     * @var string|null $clientId
     */
    protected ?string $clientId = null;

    /**
     * client_secret for OAuth2 Client Credentials Grant
     *
     * @var string|null $clientSecret
     */
    protected ?string $clientSecret = null;

    /**
     * Authorization type
     *
     * @var AuthorizationType|null $authType
     */
    protected ?AuthorizationType $authType = null;

    /**
     * Authorization token
     *
     * @var string|null $token
     */
    protected ?string $token = null;

    public function hasClientId(): bool
    {
        return ! empty($this->clientId);
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function hasClientSecret(): bool
    {
        return ! empty($this->clientSecret);
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function setClientCredentials(string $clientId, string $clientSecret): void
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function hasAuthorizationType(): bool
    {
        return $this->authType !== null;
    }

    public function getAuthorizationType(): ?AuthorizationType
    {
        return $this->authType;
    }

    public function setAuthorizationType(AuthorizationType $authType): self
    {
        $this->authType = $authType;
        return $this;
    }

    public function hasToken(): bool
    {
        return !empty($this->token);
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function setAuthorization(AuthorizationType $authType, string $token): void
    {
        $this->authType = $authType;
        $this->token = $token;
    }
}
