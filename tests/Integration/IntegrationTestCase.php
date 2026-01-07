<?php

namespace Tests\MessagingClients\DiscordClient\Integration;

use EasyHttp\MockBuilder\MockBuilder;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected Generator $faker;
    protected MockBuilder $builder;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->builder = new MockBuilder();
    }
}
