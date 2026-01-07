<?php

namespace Tests\MessagingClients\DiscordClient\Integration\DiscordClient;

use MessagingClients\DiscordClient\Authorization;
use MessagingClients\DiscordClient\DiscordClient;
use EasyHttp\MockBuilder\HttpMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\MessagingClients\DiscordClient\Integration\Concerns\HasAuthorization;
use Tests\MessagingClients\DiscordClient\Integration\IntegrationTestCase;

#[CoversClass(DiscordClient::class)]
#[UsesClass(Authorization::class)]
class UsersTest extends IntegrationTestCase
{
    use HasAuthorization;

    #[Test]
    #[DataProvider('authorizationProvider')]
    public function itCanGetCurrentUserInfo(Authorization $authorization): void
    {
        $this->builder
            ->when()
                ->methodIs('GET')
                ->pathIs('/api/v10/users/@me')
            ->then()
                ->statusCode(200)
                ->json(
                    $jsonResponse = [
                        'id' => $this->faker->numerify('###################'),
                        'username' => $this->faker->userName,
                        'global_name' => $this->faker->name,
                        'avatar' => '6c5996770c985bcd6e5b68131ff2ba04',
                        'verified' => true,
                        // among other fields
                    ]
                );

        $client = new DiscordClient($authorization);
        $client->withHandler(new HttpMock($this->builder));
        $response = $client->getCurrentUser();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($jsonResponse, $response->parseJson());
    }

    protected function invokeEndpoint(DiscordClient $client): void
    {
        $client->getCurrentUser();
    }
}
