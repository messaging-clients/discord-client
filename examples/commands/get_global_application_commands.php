<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

use MessagingClients\DiscordClient\Authorization;
use MessagingClients\DiscordClient\Constants\AuthorizationType;
use MessagingClients\DiscordClient\DiscordClient;

$token = 'your-bot-or-bearer-token-here';
$applicationId = 'your-application-id-here';
$authorizationType = AuthorizationType::BOT_TOKEN;
// you can also use AuthorizationType::BEARER_TOKEN with a token that has "applications.commands" / "applications.commands.update" scope.

$authorization = new Authorization();
$authorization->setAuthorization($authorizationType, $token);

$client = new DiscordClient($authorization);

$response = $client->getGlobalApplicationCommands($applicationId);

var_dump($response->getStatusCode(), $response->getBody());

