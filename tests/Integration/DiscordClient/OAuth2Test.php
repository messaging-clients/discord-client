<?php

namespace Integration\DiscordClient;

use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\DiscordClient;
use EasyHttp\MockBuilder\HttpMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\EasyHttp\DiscordClient\Integration\IntegrationTestCase;

#[CoversClass(DiscordClient::class)]
#[UsesClass(Authorization::class)]
class OAuth2Test extends IntegrationTestCase
{
    #[Test]
    public function itCanRequestAnAccessTokenUsingClientCredentials()
    {
        $clientId = $this->faker->uuid;
        $clientSecret = $this->faker->sha1;

        $this->builder
            ->when()
                ->methodIs('POST')
                ->pathIs('/api/oauth2/token')
            ->then()
                ->statusCode(200)
                ->json(
                    $jsonResponse = [
                    'token_type' => 'Bearer',
                    'access_token' => 'a1b2c3d4e5f6g7h8i9j0',
                    'expires_in' => 604800,
                    'scope' => 'identify',
                    ]
                );

        $authorization = new Authorization();
        $authorization->setClientCredentials($clientId, $clientSecret);
        $client = new DiscordClient($authorization);
        $client->withHandler(new HttpMock($this->builder));
        $response = $client->requestAccessToken(['identify']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($jsonResponse, $response->parseJson());
    }

    #[Test]
    public function itCanRequestAnAccessTokenUsingClientCredentialsWithMultipleScopes()
    {
        $clientId = $this->faker->uuid;
        $clientSecret = $this->faker->sha1;

        $this->builder
            ->when()
                ->methodIs('POST')
                ->pathIs('/api/oauth2/token')
            ->then()
                ->statusCode(200)
                ->json(
                    $jsonResponse = [
                    'token_type' => 'Bearer',
                    'access_token' => 'a1b2c3d4e5f6g7h8i9j0',
                    'expires_in' => 604800,
                    'scope' => 'identify messages.read',
                    ]
                );

        $authorization = new Authorization();
        $authorization->setClientCredentials($clientId, $clientSecret);
        $client = new DiscordClient($authorization);
        $client->withHandler(new HttpMock($this->builder));
        $response = $client->requestAccessToken(['identify', 'messages.read']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($jsonResponse, $response->parseJson());
    }

    #[Test]
    public function itCanHandleBadRequestsWhenRequestingTokens()
    {
        $clientId = $this->faker->uuid;
        $clientSecret = $this->faker->sha1;

        $this->builder
            ->when()
                ->methodIs('POST')
                ->pathIs('/api/oauth2/token')
            ->then()
            ->statusCode(400)
                ->json(
                    $jsonResponse = [
                    'client_id' => ['Value \" ' . $clientId . '\" is not snowflake.'],
                    ]
                );

        $authorization = new Authorization();
        $authorization->setClientCredentials($clientId, $clientSecret);
        $client = new DiscordClient($authorization);
        $client->withHandler(new HttpMock($this->builder));

        $response = $client->requestAccessToken(['activities.write']);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame($jsonResponse, $response->parseJson());
    }
}
