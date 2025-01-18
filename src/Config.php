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
     * Available units
     * @var array
     */
    private const UNITS = ['metric', 'imperial', 'standard'];

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
     * - units: metric|imperial|standard - The unit of temperature and measure: 'metric' for Celsius / metric, 'imperial' for Fahrenheit / imperial, 'standard' for Kelvin / metric.
     */
    public function __construct(array $config = [])
    {
        $defaultConfig = require __DIR__ . '/config/openweather.php';

        $this->config = array_merge($defaultConfig, $config);

        $this->validate();
    }

    public function all(): array
    {
        return $this->config;
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
     * Validate configuration array
     * @throws \Bejblade\OpenWeather\Exception\InvalidConfigurationException
     * @return void
     */
    private function validate(): void
    {

        if (empty($this->config['api_key'])) {
            throw new InvalidConfigurationException("API KEY is not set");
        }

        if (!in_array($this->config['units'], self::UNITS)) {
            throw new InvalidConfigurationException("Invalid units: {$this->config['units']}");
        }

        if (!in_array($this->config['api_version'], self::API_VERSIONS)) {
            throw new InvalidConfigurationException("Invalid API version: {$this->config['api_version']}");
        }
    }
}
