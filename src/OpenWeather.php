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
    protected function registerEndpoints(): void
    {
        $this->endpoints = [
            'weather' => new Endpoint\WeatherEndpoint($this->client, [
                'api_version' => $this->config->get('api_version'),
                'date_format' => $this->config->get('date_format'),
                'time_format' => $this->config->get('time_format'),
                'day_format' => $this->config->get('day_format'),
                'temperature' => $this->config->get('temperature'),
                'timezone' => $this->config->get('timezone'),
        ]),
        ];

        $geoConfig = [
            'api_version' => $this->config->get('geo_api_version')
        ];

        $this->geoEndpoints = [
            'direct' => new Endpoint\Geocoding\GeocodingDirectEndpoint($this->client, $geoConfig),
            'zip' => new Endpoint\Geocoding\GeocodingZipEndpoint($this->client, $geoConfig),
            'reverse' => new Endpoint\Geocoding\GeocodingReverseEndpoint($this->client, $geoConfig)
        ];
    }

    /**
     * Get Weather by Location object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @return Model\Weather
     */
    public function getWeatherByLocation(\Bejblade\OpenWeather\Model\Location $location)
    {
        return $location->getCurrentWeather($this->getEndpoint('weather'));
    }

    /**
     * Find geolocation by location name. Returns an array of `Location`
     * @param string $city_name
     * @param int $limit Response data limit. Default 1.
     * @param string|null $state_code Only available when country code is 'US'
     * @param string|null $country_code ISO 3166 country code
     * @return Model\Location[]
     */
    public function findLocationByName(string $city_name, int $limit = 1, ?string $state_code = null, ?string $country_code = null)
    {
        $query = implode(',', [$city_name, $state_code, $country_code]);
        return $this->getGeoEndpoint('direct')->call(['q' => $query, 'limit' => $limit]);
    }

    /**
     * Find geolocation by zip code. Returns an array of `Location`
     * @param string $zip_code
     * @param string $country_code ISO 3166 country code
     * @return Model\Location
     */
    public function findLocationByZipCode(string $zip_code, string $country_code)
    {
        return $this->getGeoEndpoint('zip')->call(['zip' => $zip_code . ',' .$country_code]);
    }

    /**
     * Find geolocation by zip code. Returns an array of `Location`
     * @param string $lat Latitude
     * @param string $lon Longitude
     * @param int $limit Response data limit. Default 1.
     * @return Model\Location[]
     */
    public function findLocationByCoords(string $lat, string $lon, int $limit = 1)
    {
        return $this->getGeoEndpoint('reverse')->call(['lat' => $lat, 'lon' => $lon, 'limit' => $limit]);
    }
}
