<?php

namespace Bejblade\OpenWeather;

use Bejblade\OpenWeather\Exception\InvalidConfigurationException;

/**
 * Stores configuration
 */
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
     * Current Config instance
     * @var ?Config
     */
    private static ?Config $instance = null;

    /**
     * @param array $config Array with configuration parameters
     *
     * - api_key: string - The API key for accessing the OpenWeather API.
     * - language: string - The language code (ISO 639-1) for API responses. Defaults to 'en'.
     * - date_format: string - PHP date format for displaying dates (default 'd/m/Y').
     * - time_format: string - PHP date format for displaying times (default 'H:i').
     * - day_format: string - PHP date format for displaying days of the week (replaces day format in `date_format`, default 'l').
     * - timezone: string - PHP supported timezone (default 'UTC')
     * - units: metric|imperial|standard - The unit of temperature and measure: 'metric' for Celsius / metric, 'imperial' for Fahrenheit / imperial, 'standard' for Kelvin / metric.
     */
    private function __construct(array $config = [])
    {
        $defaultConfig = require __DIR__ . '/config/openweather.php';

        $this->config = array_merge($defaultConfig, $config);

        $this->validate();
    }

    /**
     * Get current Config instance.
     *
     * When run for the first time it will initialize new instance with `config` array.
     *
     * @param array $config Array with configuration parameters
     * - api_key: string - The API key for accessing the OpenWeather API.
     * - language: string - The language code (ISO 639-1) for API responses. Defaults to 'en'.
     * - date_format: string - PHP date format for displaying dates (default 'd/m/Y').
     * - time_format: string - PHP date format for displaying times (default 'H:i').
     * - day_format: string - PHP date format for displaying days of the week (replaces day format in `date_format`, default 'l').
     * - timezone: string - PHP supported timezone (default 'UTC')
     * - units: metric|imperial|standard - The unit of temperature and measure: 'metric' for Celsius / metric, 'imperial' for Fahrenheit / imperial, 'standard' for Kelvin / metric.
     *
     * @return Config
     */
    public static function configuration(array $config = [])
    {
        return self::$instance ?? self::$instance = new self($config);
    }

    /**
     * Get configuration
     * @return array
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Get configuration key
     * @param mixed $key
     * @throws \InvalidArgumentException When key is not found
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
     * @throws \Bejblade\OpenWeather\Exception\InvalidConfigurationException When configuration is invalid
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
    }
}
