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
class CreateGuildApplicationCommandTest extends IntegrationTestCase
{
    use HasAuthorization;

    #[Test]
    #[DataProvider('authorizationProvider')]
    public function itCanCreateGuildApplicationCommand(Authorization $authorization): void
    {
        $name = $this->faker->word();
        $applicationId = $this->faker->numerify('###################');
        $guildId = $this->faker->numerify('###################');
        $commandId = $this->faker->numerify('###################');

        $command = [
            'name' => $name,
            'description' => 'A guild-specific command created via PHP SDK',
            'type' => 1,
            'integration_types' => [0, 1],
            'contexts' => [0, 1, 2]
        ];

        $this->builder
            ->when()
                ->methodIs('POST')
                ->pathMatch('/api\/v10\/applications\/' . $applicationId . '\/guilds\/' . $guildId . '\/commands' . '/')
            ->then()
                ->statusCode(200)
                ->json(
                    $jsonResponse = [
                        'id' => $commandId,
                        'application_id' => $applicationId,
                        'version' => $this->faker->numerify('###################'),
                        'name' => $name,
                        'description' => 'A guild-specific command created via PHP SDK',
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
                );

        $client = new DiscordClient($authorization);
        $client->withHandler(new HttpMock($this->builder));
        $response = $client->createGuildApplicationCommand($applicationId, $guildId, $command);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($jsonResponse, $response->parseJson());
    }

    protected function invokeEndpoint(DiscordClient $client): void
    {
        $applicationId = $this->faker->numerify('###################');
        $guildId = $this->faker->numerify('###################');
        $command = [
            'name' => $this->faker->word(),
            'description' => 'A guild-specific command created via PHP SDK',
            'type' => 1,
        ];

        $client->createGuildApplicationCommand($applicationId, $guildId, $command);
    }
}
