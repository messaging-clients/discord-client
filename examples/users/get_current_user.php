<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

use MessagingClients\DiscordClient\Authorization;
use MessagingClients\DiscordClient\Constants\AuthorizationType;
use MessagingClients\DiscordClient\DiscordClient;

$token = 'your-bot-or-bearer-token-here';
$authorizationType = AuthorizationType::BOT_TOKEN;
// you can also use AuthorizationType::BEARER_TOKEN with a token that has "identify"/"email" scopes

$authorization = new Authorization();
$authorization->setAuthorization($authorizationType, $token);

$client = new DiscordClient($authorization);

$response = $client->getCurrentUser();

var_dump($response->getStatusCode(), $response->getBody());

