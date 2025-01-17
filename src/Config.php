<?php

namespace Bejblade\OpenWeather;

use Bejblade\OpenWeather\Exception\InvalidConfigurationException;

final class Config
{
    /**
     * Configuration array
     * @var array
     */
    private array $config;

    /**
     * Available temperature units
     * @var array
     */
    private const TEMPERATURE_UNITS = ['c', 'f', 'k'];

    /**
     * Available API versions
     * @var array
     */
    private const API_VERSIONS = ['2.5', '3.0'];

    /**
     * Constructor
     * @param array $config Array with configuration parameters
     * - api_key: string - The API key for accessing the OpenWeather API.
     * - language: string - The language code (ISO 639-1) for API responses. Defaults to 'en'.
     * - date_format: string - PHP date format for displaying dates (default 'd/m/Y').
     * - time_format: string - PHP date format for displaying times (default 'h:i A').
     * - day_format: string - PHP date format for displaying days of the week (default, 'l').
     * - timezone: string - PHP supported timezone (default 'UTC')
     * - temperature: 'c'|'f'|'k' - The unit of temperature: 'c' for Celsius, 'f' for Fahrenheit, 'k' for Kelvin.
     */
    public function __construct(array $config = [])
    {
        $defaultConfig = require __DIR__ . '/config/openweather.php';

        $this->config = array_merge($defaultConfig, $config);

        $this->validate();
        $this->config['temperature'] = $this->setTemperature($this->config['temperature']);
    }

    /**
     * Get configuration key
     * @param mixed $key
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->config)) {
            throw new \InvalidArgumentException("Configuration key {$key} not found");
        }

        return $this->config[$key];
    }

    /**
     * Convert temperature to valid API format
     * @param string $temperature
     * @return string|null
     */
    private function setTemperature($temperature): string|null
    {
        return match ($temperature) {
            'c' => 'metric',
            'f' => 'imperial',
            'k' => null
        };
    }

    /**
     * Validate configuration array
     * @throws \Bejblade\OpenWeather\Exception\InvalidConfigurationException
     * @return void
     */
    private function validate(): void
    {

        if (empty($this->config['api_key'])) {
            throw new InvalidConfigurationException("API KEY is not set");
        }

        if (!in_array($this->config['temperature'], self::TEMPERATURE_UNITS)) {
            throw new InvalidConfigurationException("Invalid temparature unit: {$this->config['temperature']}");
        }

        if (!in_array($this->config['api_version'], self::API_VERSIONS)) {
            throw new InvalidConfigurationException("Invalid API version: {$this->config['api_version']}");
        }
    }
}
