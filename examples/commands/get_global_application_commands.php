<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

use EasyHttp\DiscordClient\Authorization;
use EasyHttp\DiscordClient\Constants\AuthorizationType;
use EasyHttp\DiscordClient\DiscordClient;

$token = 'your-bot-or-bearer-token-here';
$applicationId = 'your-application-id-here';
$authorizationType = AuthorizationType::BOT_TOKEN;
// you can also use AuthorizationType::BEARER_TOKEN with a token that has "applications.commands.read" scope.

$authorization = new Authorization();
$authorization->setAuthorization($authorizationType, $token);

$client = new DiscordClient($authorization);

$response = $client->getGlobalApplicationCommands($applicationId);

var_dump($response->getStatusCode(), $response->getBody());

