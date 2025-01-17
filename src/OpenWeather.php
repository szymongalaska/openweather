<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather;

use Bejblade\OpenWeather\OpenWeatherClient;
use Bejblade\OpenWeather\Interface\EndpointInterface;

class OpenWeather
{
    /**
     * Configuration
     * @var Config
     */
    protected Config $config;

    /**
     * Client
     * @var OpenWeatherClient
     */
    protected OpenWeatherClient $client;

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

    /**
     * Constructor method. Set configuration, client and register endpoints
     * @param array $config Array with configuration parameters for Config class.
     */
    public function __construct(array $config = [])
    {
        $this->config = new Config($config);

        $clientConfig = [
            'base_uri' => $this->config->get('url'),
            'query' => [
                'appid' => $this->config->get('api_key'),
                'lang' => $this->config->get('language')
            ]
        ];

        $this->client = new OpenWeatherClient($clientConfig);
        $this->registerEndpoints();
    }

    /**
     * Get endpoint from $endpoints array
     * @param string $endpoint Name of endpoint
     * @return \Bejblade\OpenWeather\Interface\EndpointInterface
     */
    protected function getEndpoint(string $endpoint): EndpointInterface
    {
        return $this->endpoints[$endpoint];
    }

    /**
     * Get Geocoding endpoint from $geoEndpoints array
     * @param string $endpoint Name of endpoint
     * @return \Bejblade\OpenWeather\Interface\EndpointInterface
     */
    protected function getGeoEndpoint(string $endpoint): EndpointInterface
    {
        return $this->geoEndpoints[$endpoint];
    }

    /**
     * Register endpoints in their corresponding arrays
     * @return void
     */
    private function registerEndpoints(): void
    {
        $this->endpoints = [

        ];

        $this->geoEndpoints = [
            'direct' => new Endpoint\Geocoding\GeocodingDirectEndpoint($this->client),
            'zip' => new Endpoint\Geocoding\GeocodingZipEndpoint($this->client),
            'reverse' => new Endpoint\Geocoding\GeocodingReverseEndpoint($this->client)
        ];
    }
}
