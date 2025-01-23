<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Bejblade\OpenWeather\Config;

abstract class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::configuration(['api_key' => 'testApiKey']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function fixture(string $filename): array
    {
        return json_decode(file_get_contents(__DIR__ . '/fixture/' . $filename . '.json'), true);
    }
}
