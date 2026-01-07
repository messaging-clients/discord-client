<?php

namespace Tests\MessagingClients\DiscordClient\Integration\DiscordClient;

use MessagingClients\DiscordClient\Authorization;
use MessagingClients\DiscordClient\Constants\IntegrationType;
use MessagingClients\DiscordClient\Constants\InteractionContextType;
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
class GlobalCommandsTest extends IntegrationTestCase
{
    use HasAuthorization;

    #[Test]
    #[DataProvider('authorizationProvider')]
    public function itCanBulkOverwriteGlobalApplicationCommands(Authorization $authorization)
    {
        $name = $this->faker->word();
        $applicationId = $this->faker->numerify('###################');

        $commands = [
            [
                'name' => $name,
                'description' => 'A command created via PHP SDK',
                'type' => 1,
                'integration_types' => [0, 1],
                'contexts' => [0, 1, 2]
            ]
        ];

        $this->builder
            ->when()
                ->methodIs('PUT')
                ->pathMatch('/api\/v10\/applications\/' . $applicationId . '\/commands' . '/')
            ->then()
                ->statusCode(200)
                ->json(
                    $jsonResponse = [
                    [
                        'id' => $this->faker->numerify('###################'),
                        'application_id' => $applicationId,
                        'version' => $this->faker->numerify('###################'),
                        'name' => $name,
                        'description' => 'A command created via PHP SDK',
                        'type' => 1,
                        'integration_types' => [
                            IntegrationType::GUILD_INSTALL->value,
                            IntegrationType::USER_INSTALL->value
                        ],
                        'contexts' => [
                            InteractionContextType::GUILD->value,
                            InteractionContextType::BOT_DM->value,
                            InteractionContextType::PRIVATE_CHANNEL->value
                        ]
                    ]
                    ]
                );

        $client = new DiscordClient($authorization);
        $client->withHandler(new HttpMock($this->builder));
        $response = $client->bulkGlobalApplicationCommands($applicationId, $commands);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($jsonResponse, $response->parseJson());
    }

    protected function invokeEndpoint(DiscordClient $client): void
    {
        $applicationId = $this->faker->numerify('###################');
        $commands = [
            [
                'name' => $this->faker->word(),
                'description' => 'A command created via PHP SDK',
                'type' => 1,
            ]
        ];

        $client->bulkGlobalApplicationCommands($applicationId, $commands);
    }
}
