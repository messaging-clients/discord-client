<?php

namespace Tests\MessagingClients\DiscordClient\Unit;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    protected Generator $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }
}
