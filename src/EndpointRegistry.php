<?php
declare(strict_types=1);

namespace Bejblade\OpenWeather;

use Bejblade\OpenWeather\Interface\EndpointInterface;

class EndpointRegistry
{
    /**
     * Array of endpoints
     * @var EndpointInterface[]
     */
    private array $endpoints = [];

    /**
     * Array of Geocoding endpoints
     * @var EndpointInterface[]
     */
    private array $geoEndpoints = [];

    private static ?EndpointRegistry $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function registerEndpoint(string $name, EndpointInterface $endpoint): self
    {
        $this->endpoints[$name] = $endpoint;

        return $this;
    }

    public function registerGeoEndpoint(string $name, EndpointInterface $endpoint): self
    {
        $this->geoEndpoints[$name] = $endpoint;

        return $this;
    }

       /**
     * Get endpoint from $endpoints array
     * @param string $endpoint Name of endpoint
     * @return \Bejblade\OpenWeather\Interface\EndpointInterface
     */
    public function getEndpoint(string $endpoint): EndpointInterface
    {
        return $this->endpoints[$endpoint];
    }

    /**
     * Get Geocoding endpoint from $geoEndpoints array
     * @param string $endpoint Name of endpoint
     * @return \Bejblade\OpenWeather\Interface\EndpointInterface
     */
    public function getGeoEndpoint(string $endpoint): EndpointInterface
    {
        return $this->geoEndpoints[$endpoint];
    }
}