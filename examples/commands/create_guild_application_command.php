<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

use MessagingClients\DiscordClient\Authorization;
use MessagingClients\DiscordClient\Constants\ApplicationCommandType;
use MessagingClients\DiscordClient\Constants\AuthorizationType;
use MessagingClients\DiscordClient\DiscordClient;

$token = 'your-bot-or-bearer-token-here';
$applicationId = 'your-application-id-here';
$guildId = 'your-guild-id-here';
$authorizationType = AuthorizationType::BOT_TOKEN;
// you can also use AuthorizationType::BEARER_TOKEN with a token that has "applications.commands.update" scope.

$authorization = new Authorization();
$authorization->setAuthorization($authorizationType, $token);

$client = new DiscordClient($authorization);

$command = [
    'name' => 'guild-command',
    'description' => 'A guild-specific command created via PHP SDK',
    'type' => ApplicationCommandType::CHAT_INPUT->value,
];

$response = $client->createGuildApplicationCommand($applicationId, $guildId, $command);

var_dump($response->getStatusCode(), $response->getBody());

