<?php

namespace Tests\EasyHttp\DiscordClient\Integration\DiscordClient;

use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\Constants\IntegrationType;
use EasyHttp\DiscordClient\Constants\InteractionContextType;
use EasyHttp\DiscordClient\DiscordClient;
use EasyHttp\MockBuilder\HttpMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\EasyHttp\DiscordClient\Integration\Concerns\HasAuthorization;
use Tests\EasyHttp\DiscordClient\Integration\IntegrationTestCase;

#[CoversClass(DiscordClient::class)]
#[UsesClass(Authorization::class)]
class GetGlobalApplicationCommandsTest extends IntegrationTestCase
{
    use HasAuthorization;

    #[Test]
    #[DataProvider('authorizationProvider')]
    public function itCanGetGlobalApplicationCommands(Authorization $authorization): void
    {
        $applicationId = $this->faker->numerify('###################');
        $commandId = $this->faker->numerify('###################');
        $name = $this->faker->word();

        $this->builder
            ->when()
                ->methodIs('GET')
                ->pathMatch('/api\/v10\/applications\/' . $applicationId . '\/commands' . '/')
            ->then()
                ->statusCode(200)
                ->json(
                    $jsonResponse = [
                        [
                            'id' => $commandId,
                            'application_id' => $applicationId,
                            'version' => $this->faker->numerify('###################'),
                            'name' => $name,
                            'description' => 'A test command',
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
        $response = $client->getGlobalApplicationCommands($applicationId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($jsonResponse, $response->parseJson());
    }

    protected function invokeEndpoint(DiscordClient $client): void
    {
        $applicationId = $this->faker->numerify('###################');
        $client->getGlobalApplicationCommands($applicationId);
    }
}

