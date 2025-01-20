<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather;

use Bejblade\OpenWeather\Interface\EndpointInterface;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\OpenWeatherClient;

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
     * Endpoints array
     * @var array
     */
    private array $endpointRegistry;

    /**
     * Constructor method. Set configuration, client and register endpoints
     * @param array $config Array with configuration parameters for Config class.
     */
    public function __construct(array $config = [])
    {
        $this->initializeConfig($config);

        $clientConfig = [
            'base_uri' => Config::configuration()->get('url'),
            'query' => [
                'appid' => Config::configuration()->get('api_key'),
                'lang' => Config::configuration()->get('language')
            ]
        ];

        $this->client = new OpenWeatherClient($clientConfig);
        $this->registerEndpoints();
    }

    private function initializeConfig(array $config = [])
    {
        Config::configuration($config);
    }

    /**
     * Register endpoints in their array
     * @return void
     */
    protected function registerEndpoints(): void
    {
        $this->endpointRegistry = [
            'weather' => new Endpoint\WeatherEndpoint($this->client),
            'forecast' => new Endpoint\ForecastEndpoint($this->client),
            'geo.direct' => new Endpoint\Geocoding\GeocodingDirectEndpoint($this->client),
            'geo.zip' => new Endpoint\Geocoding\GeocodingZipEndpoint($this->client),
            'geo.reverse' => new Endpoint\Geocoding\GeocodingReverseEndpoint($this->client),
        ];
    }

    /**
     * Getter for endpoints
     * @param mixed $name
     * @return \Bejblade\OpenWeather\Interface\EndpointInterface|\Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface
     */
    protected function getEndpoint($name): EndpointInterface|LocationAwareEndpointInterface
    {
        return $this->endpointRegistry[$name];
    }

    /**
     * Get Weather by Location object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @return Model\Weather
     */
    public function getWeatherByLocation(\Bejblade\OpenWeather\Model\Location $location): Model\Weather|null
    {
        if (!$location->hasWeather() || $location->getWeather()->isUpdateAvailable()) {
            $location->setWeather($this->getEndpoint('weather')->callWithLocation($location));
        }

        return $location->getWeather();
    }

    /**
     * Get Forecast by Location object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @return Model\Forecast|null
     */
    public function getForecastByLocation(\Bejblade\OpenWeather\Model\Location $location): Model\Forecast|null
    {
        $location->setForecast($this->getEndpoint('forecast')->callWithLocation($location));
        return $location->getForecast();
    }

    /**
     * Find geolocation by location name. Returns an array of `Location`
     * @param string $cityName
     * @param int $limit Response data limit. Default 1.
     * @param string|null $stateCode Only available when country code is 'US'
     * @param string|null $countryCode ISO 3166 country code
     * @return Model\Location[]
     */
    public function findLocationByName(string $cityName, int $limit = 1, ?string $stateCode = null, ?string $countryCode = null)
    {
        $query = implode(',', array_filter([$cityName, $stateCode, $countryCode]));
        return $this->getEndpoint('geo.direct')->call(['q' => $query, 'limit' => $limit]);
    }

    /**
     * Find geolocation by zip code. Returns an array of `Location`
     * @param string $zipCode
     * @param string $countryCode ISO 3166 country code
     * @return Model\Location
     */
    public function findLocationByZipCode(string $zipCode, string $countryCode)
    {
        return $this->getEndpoint('geo.zip')->call(['zip' => $zipCode . ',' .$countryCode]);
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
        return $this->getEndpoint('geo.reverse')->call(['lat' => $lat, 'lon' => $lon, 'limit' => $limit]);
    }
}
