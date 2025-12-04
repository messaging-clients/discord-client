<?php

namespace EasyHttp\tests;

use EasyHttp\DiscordClient\DiscordClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DiscordClient::class)]
class DiscordClientTest extends TestCase
{
    #[Test]
    public function testExample(): void
    {
        $client = new DiscordClient();
        $this->assertTrue(true);
    }
}
