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
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $defaultConfig = require __DIR__.'/config/openweather.php';

        $this->config = array_merge($defaultConfig, $config);

        $this->validate();
        $this->setTemperature($this->config['temperature']);
    }

    /**
     * Convert temperature to valid API format
     * @param string $temperature
     * @return void
     */
    private function setTemperature(&$temperature): void
    {
        switch ($temperature) {
            case 'c':
                $temperature = 'metric';
                break;
            case 'f':
                $temperature = 'imperial';
                break;
            case 'k':
                $temperature = null;
                break;
        }
    }

    /**
     * Validate configuration array
     * @throws \Bejblade\OpenWeather\Exception\InvalidConfigurationException
     * @return void
     */
    private function validate(): void
    {
        if (empty($this->config['api_key'])) {
            throw new InvalidConfigurationException('API key is not set');
        }

        if (!in_array($this->config['temperature'], self::TEMPERATURE_UNITS)) {
            throw new InvalidConfigurationException("Invalid temparature unit: {$this->config['temperature']}");
        }

        if (!in_array($this->config['api_version'], self::API_VERSIONS)) {
            throw new InvalidConfigurationException("Invalid API version: {$this->config['api_version']}");
        }
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
}
