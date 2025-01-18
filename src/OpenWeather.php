<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather;

use Bejblade\OpenWeather\OpenWeatherClient;
use Bejblade\OpenWeather\EndpointRegistry;

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

    private EndpointRegistry $endpointRegistry;

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
        $this->createEndpointRegistry();
    }

    /**
     * Register endpoints in their corresponding arrays
     * @return void
     */
    protected function createEndpointRegistry(): void
    {
        $this->endpointRegistry = EndpointRegistry::getRegistry();

        $this->endpointRegistry
            ->registerEndpoint('weather', new Endpoint\WeatherEndpoint($this->client, $this->config->all()))
            ->registerEndpoint('forecast', new Endpoint\ForecastEndpoint($this->client, $this->config->all()))
            ->registerEndpoint('geo.direct', new Endpoint\Geocoding\GeocodingDirectEndpoint($this->client, ['api_version' => '1.0']))
            ->registerEndpoint('geo.zip', new Endpoint\Geocoding\GeocodingZipEndpoint($this->client, ['api_version' => '1.0']))
            ->registerEndpoint('geo.reverse', new Endpoint\Geocoding\GeocodingReverseEndpoint($this->client, ['api_version' => '1.0']));
    }


    /**
     * Get Weather by Location object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @return Model\Weather
     */
    public function getWeatherByLocation(\Bejblade\OpenWeather\Model\Location $location): Model\Weather|null
    {
        if (!$location->hasWeather() || $location->getWeather()->isUpdateAvailable()) {
            $location->setWeather($this->endpointRegistry->getEndpoint('weather')->callWithLocation($location));
        }

        return $location->getWeather();
    }

    /**
     * Get Forecast by Location object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @return Model\Weather
     */
    public function getForecastByLocation(\Bejblade\OpenWeather\Model\Location $location): Model\Forecast|null
    {
        $location->setForecast($this->endpointRegistry->getEndpoint('forecast')->callWithLocation($location));
        return $location->getForecast();
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
        $query = implode(',', array_filter([$city_name, $state_code, $country_code]));
        return $this->endpointRegistry->getEndpoint('geo.direct')->call(['q' => $query, 'limit' => $limit]);
    }

    /**
     * Find geolocation by zip code. Returns an array of `Location`
     * @param string $zip_code
     * @param string $country_code ISO 3166 country code
     * @return Model\Location
     */
    public function findLocationByZipCode(string $zip_code, string $country_code)
    {
        return $this->endpointRegistry->getEndpoint('geo.zip')->call(['zip' => $zip_code . ',' .$country_code]);
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
        return $this->endpointRegistry->getEndpoint('geo.reverse')->call(['lat' => $lat, 'lon' => $lon, 'limit' => $limit]);
    }
}
