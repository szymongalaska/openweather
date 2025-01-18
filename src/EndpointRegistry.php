<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather;

use Bejblade\OpenWeather\Interface\EndpointInterface;

/**
 * Endpoint registry class for accessing endpoints anywhere
 */
class EndpointRegistry
{
    /**
     * Array of endpoints
     * @var EndpointInterface[]
     */
    private array $endpoints = [];

    /**
     * Instance of class
     * @var EndpointRegistry|null
     */
    private static ?EndpointRegistry $instance = null;

    private function __construct()
    {
    }

    /**
     * Get class instance
     * @return \Bejblade\OpenWeather\EndpointRegistry
     */
    public static function getRegistry(): self
    {
        return self::$instance ?? self::$instance = new self();
    }

    /**
     * Access endpoint by its name from endpoints registry
     * @param string $endpoint Name of endpoint
     * @return \Bejblade\OpenWeather\Interface\EndpointInterface
     */
    public function getEndpoint(string $endpoint): EndpointInterface
    {
        return $this->endpoints[$endpoint];
    }

    /**
     * Register endpoint
     * @param string $name Name of endpoint, used to access endpoint
     * @param \Bejblade\OpenWeather\Interface\EndpointInterface $endpoint
     * @return \Bejblade\OpenWeather\EndpointRegistry
     */
    public function registerEndpoint(string $name, EndpointInterface $endpoint): self
    {
        $this->endpoints[$name] = $endpoint;

        return $this;
    }
}
