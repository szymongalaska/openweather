<?php

use PHPUnit\Framework\TestCase;
use Bejblade\OpenWeather\Config;
use Bejblade\OpenWeather\Exception\InvalidConfigurationException;

class ConfigTest extends TestCase
{
    public function test_it_throws_api_key_exception(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        Config::configuration();
    }

    public function test_it_throws_invalid_temperature_exception(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        Config::configuration(['api_key' => 'api_key', 'units' => 'invalidTemperature']);
    }

    public function test_get_throws_invalid_argument_exception(): void
    {
        $config = Config::configuration(['api_key' => 'api_key']);
        $this->expectException(\InvalidArgumentException::class);
        $config->get('invalidKey');
    }

    public function test_get_returns_value(): void
    {
        $config = Config::configuration(['api_key' => 'api_key']);
        $this->assertEquals('api_key', $config->get('api_key'));
    }
}
