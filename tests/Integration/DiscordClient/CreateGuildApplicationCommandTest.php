<?php

namespace Tests\MessagingClients\DiscordClient\Integration\DiscordClient;

use MessagingClients\DiscordClient\Authorization;
use MessagingClients\DiscordClient\Constants\ApplicationCommandType;
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
            'type' => ApplicationCommandType::CHAT_INPUT->value,
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
                        'type' => ApplicationCommandType::CHAT_INPUT->value,
                        'application_id' => $applicationId,
                        'version' => $this->faker->numerify('###################'),
                        'guild_id' => $guildId,
                        'name' => $name,
                        'description' => 'A guild-specific command created via PHP SDK',
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
            'type' => ApplicationCommandType::CHAT_INPUT->value,
        ];

        $client->createGuildApplicationCommand($applicationId, $guildId, $command);
    }
}
