<?php

namespace Tests\MessagingClients\DiscordClient\Unit;

use MessagingClients\DiscordClient\Authorization;
use MessagingClients\DiscordClient\Constants\AuthorizationType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\MessagingClients\DiscordClient\Unit\UnitTestCase;

#[CoversClass(Authorization::class)]
class AuthorizationTest extends UnitTestCase
{
    #[Test]
    public function itInitializesWithNullValues(): void
    {
        $authorization = new Authorization();

        $this->assertFalse($authorization->hasClientId());
        $this->assertFalse($authorization->hasClientSecret());
        $this->assertFalse($authorization->hasAuthorizationType());
        $this->assertFalse($authorization->hasToken());
        $this->assertNull($authorization->getAuthorizationType());
        $this->assertNull($authorization->getToken());
    }

    #[Test]
    public function itCanSetAndGetClientId(): void
    {
        $clientId = $this->faker->uuid;
        $authorization = new Authorization();

        $result = $authorization->setClientId($clientId);

        $this->assertSame($authorization, $result);
        $this->assertTrue($authorization->hasClientId());
        $this->assertSame($clientId, $authorization->getClientId());
    }

    #[Test]
    public function itCanSetAndGetClientSecret(): void
    {
        $clientSecret = $this->faker->sha1;
        $authorization = new Authorization();

        $result = $authorization->setClientSecret($clientSecret);

        $this->assertSame($authorization, $result);
        $this->assertTrue($authorization->hasClientSecret());
        $this->assertSame($clientSecret, $authorization->getClientSecret());
    }

    #[Test]
    public function itCanSetClientCredentials(): void
    {
        $clientId = $this->faker->uuid;
        $clientSecret = $this->faker->sha1;
        $authorization = new Authorization();

        $authorization->setClientCredentials($clientId, $clientSecret);

        $this->assertTrue($authorization->hasClientId());
        $this->assertTrue($authorization->hasClientSecret());
        $this->assertSame($clientId, $authorization->getClientId());
        $this->assertSame($clientSecret, $authorization->getClientSecret());
    }

    #[Test]
    public function itCanSetAndGetAuthorizationType(): void
    {
        $authorization = new Authorization();
        $authType = AuthorizationType::BEARER_TOKEN;

        $result = $authorization->setAuthorizationType($authType);

        $this->assertSame($authorization, $result);
        $this->assertTrue($authorization->hasAuthorizationType());
        $this->assertSame($authType, $authorization->getAuthorizationType());
    }

    #[Test]
    public function itCanSetAndGetToken(): void
    {
        $token = $this->faker->sha1;
        $authorization = new Authorization();

        $result = $authorization->setToken($token);

        $this->assertSame($authorization, $result);
        $this->assertTrue($authorization->hasToken());
        $this->assertSame($token, $authorization->getToken());
    }

    #[Test]
    public function itReturnsFalseForEmptyClientId(): void
    {
        $authorization = new Authorization();
        $authorization->setClientId('');

        $this->assertFalse($authorization->hasClientId());
    }

    #[Test]
    public function itReturnsFalseForEmptyClientSecret(): void
    {
        $authorization = new Authorization();
        $authorization->setClientSecret('');

        $this->assertFalse($authorization->hasClientSecret());
    }

    #[Test]
    public function itReturnsFalseForEmptyToken(): void
    {
        $authorization = new Authorization();
        $authorization->setToken('');

        $this->assertFalse($authorization->hasToken());
    }

    #[Test]
    public function itCanSetBearerTokenAuthorization(): void
    {
        $token = $this->faker->sha1;
        $authorization = new Authorization();

        $authorization->setAuthorization(AuthorizationType::BEARER_TOKEN, $token);

        $this->assertTrue($authorization->hasAuthorizationType());
        $this->assertTrue($authorization->hasToken());
        $this->assertSame(AuthorizationType::BEARER_TOKEN, $authorization->getAuthorizationType());
        $this->assertSame($token, $authorization->getToken());
    }

    #[Test]
    public function itCanSetBotTokenAuthorization(): void
    {
        $token = $this->faker->sha1;
        $authorization = new Authorization();

        $authorization->setAuthorization(AuthorizationType::BOT_TOKEN, $token);

        $this->assertTrue($authorization->hasAuthorizationType());
        $this->assertTrue($authorization->hasToken());
        $this->assertSame(AuthorizationType::BOT_TOKEN, $authorization->getAuthorizationType());
        $this->assertSame($token, $authorization->getToken());
    }

    #[Test]
    public function itSupportsFluentInterface(): void
    {
        $clientId = $this->faker->uuid;
        $clientSecret = $this->faker->sha1;
        $token = $this->faker->sha1;
        $authType = AuthorizationType::BEARER_TOKEN;

        $authorization = new Authorization();
        $result = $authorization
            ->setClientId($clientId)
            ->setClientSecret($clientSecret)
            ->setAuthorizationType($authType)
            ->setToken($token);

        $this->assertSame($authorization, $result);
        $this->assertSame($clientId, $authorization->getClientId());
        $this->assertSame($clientSecret, $authorization->getClientSecret());
        $this->assertSame($authType, $authorization->getAuthorizationType());
        $this->assertSame($token, $authorization->getToken());
    }
}
