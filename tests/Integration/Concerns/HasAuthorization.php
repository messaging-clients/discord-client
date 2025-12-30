<?php

namespace Tests\EasyHttp\DiscordClient\Integration;

use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\DiscordClient;
use EasyHttp\DiscordClient\Exceptions\MissingAuthorizationTypeException;
use EasyHttp\DiscordClient\Exceptions\MissingTokenException;
use EasyHttp\MockBuilder\HttpMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(DiscordClient::class)]
class DiscordClientTest extends IntegrationTestCase
{
    #[Test]
    public function itThrowsAnExceptionWhenTokenIsMissing(): void
    {
        $this->expectException(MissingTokenException::class);
        $this->expectExceptionMessage('Authorization token is not set.');

        $message = $this->faker->sentence();
        $channelId = $this->faker->uuid;

        $client = new DiscordClient(new Authorization());
        $client->withHandler(new HttpMock($this->builder));
        $client->createMessage($channelId, $message);
    }

    #[Test]
    public function itThrowsAnExceptionWhenAuthTypeIsMissing(): void
    {
        $this->expectException(MissingAuthorizationTypeException::class);
        $this->expectExceptionMessage('Authorization type is not set.');

        $message = $this->faker->sentence();
        $channelId = $this->faker->uuid;

        $authorization = new Authorization();
        $authorization->setToken($this->faker->sha1);
        $client = new DiscordClient($authorization);
        $client->withHandler(new HttpMock($this->builder));
        $client->createMessage($channelId, $message);
    }
}
