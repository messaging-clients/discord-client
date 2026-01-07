<?php

require_once(__DIR__ . '/../../vendor/autoload.php');

use MessagingClients\DiscordClient\Authorization;
use MessagingClients\DiscordClient\DiscordClient;

$clientId = 'your-client-id-here';
$clientSecret = 'your-client-secret-here';

$authorization = new Authorization();
$authorization->setClientCredentials($clientId, $clientSecret);
$client = new DiscordClient($authorization);

$response = $client->requestAccessToken(['identify', 'email']);

var_dump($response->getStatusCode(), $response->getBody());
