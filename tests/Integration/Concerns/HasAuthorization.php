<?php

namespace Tests\EasyHttp\DiscordClient\Integration\Concerns;

use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\Constants\AuthorizationType;
use EasyHttp\DiscordClient\DiscordClient;
use EasyHttp\DiscordClient\Exceptions\MissingAuthorizationTypeException;
use EasyHttp\DiscordClient\Exceptions\MissingTokenException;
use EasyHttp\MockBuilder\HttpMock;
use Faker\Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(DiscordClient::class)]
trait HasAuthorization
{
    abstract protected function invokeEndpoint(DiscordClient $client): void;

    #[Test]
    public function itThrowsAnExceptionWhenTokenIsMissing(): void
    {
        $this->expectException(MissingTokenException::class);
        $this->expectExceptionMessage('Authorization token is not set.');

        $client = new DiscordClient(new Authorization());
        $client->withHandler(new HttpMock($this->builder));

        $this->invokeEndpoint($client);
    }

    #[Test]
    public function itThrowsAnExceptionWhenAuthTypeIsMissing(): void
    {
        $this->expectException(MissingAuthorizationTypeException::class);
        $this->expectExceptionMessage('Authorization type is not set.');

        $authorization = new Authorization();
        $authorization->setToken($this->faker->sha1);
        $client = new DiscordClient($authorization);

        $this->invokeEndpoint($client);
    }

    public static function authorizationProvider(): array
    {
        $faker = Factory::create();
        // phpcs:ignore Zend.NamingConventions.ValidVariableName.ContainsNumbers
        $bearerToken = $faker->sha1;
        // phpcs:ignore Zend.NamingConventions.ValidVariableName.ContainsNumbers
        $botToken = $faker->sha1;

        $bearerAuthorization = new Authorization();
        $bearerAuthorization->setAuthorization(AuthorizationType::BEARER_TOKEN, $bearerToken);

        $botAuthorization = new Authorization();
        $botAuthorization->setAuthorization(AuthorizationType::BOT_TOKEN, $botToken);

        return [
            'Bearer Token' => [$bearerAuthorization],
            'Bot Token' => [$botAuthorization],
        ];
    }
}
