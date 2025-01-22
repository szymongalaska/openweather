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
            'air.pollution' => new Endpoint\AirPollution\AirPollutionEndpoint($this->client),
            'air.forecast' => new Endpoint\AirPollution\AirPollutionForecastEndpoint($this->client),
            'air.history' => new Endpoint\AirPollution\AirPollutionHistoryEndpoint($this->client),
            'onecall' => new Endpoint\OneCall\WeatherAndForecastOneCallEndpoint($this->client),
            'onecall.timemachine' => new Endpoint\OneCall\TimemachineOneCallEndpoint($this->client),
            'onecall.aggregation' => new Endpoint\OneCall\DailyAggregationOneCallEndpoint($this->client),
            'onecall.overview' => new Endpoint\OneCall\WeatherOverviewOneCallEndpoint($this->client),
        ];
    }

    /**
     * Getter for endpoints
     * @param string $name
     * @return \Bejblade\OpenWeather\Interface\EndpointInterface|\Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface
     */
    protected function getEndpoint(string $name): EndpointInterface|LocationAwareEndpointInterface
    {
        return $this->endpointRegistry[$name];
    }

    /**
     * Get `Weather` by `Location` object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @return Model\Weather
     */
    public function getWeather(\Bejblade\OpenWeather\Model\Location $location): Model\Weather|null
    {
        if (!$location->hasWeather() || $location->getWeather()->isUpdateAvailable()) {
            $location->setWeather($this->getEndpoint('weather')->callWithLocation($location));
        }

        return $location->getWeather();
    }

    /**
     * Get `Forecast` by `Location` object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @return Model\Collection\WeatherCollection|null
     */
    public function getForecast(\Bejblade\OpenWeather\Model\Location $location): Model\Collection\WeatherCollection|null
    {
        $location->setForecast($this->getEndpoint('forecast')->callWithLocation($location));
        return $location->getForecast();
    }

    /**
     * Find geolocation by location name. Returns `Location` object when `limit` is 1 or an array of `Location`
     * @param string $cityName
     * @param int $limit Response data limit. Default 1.
     * @param string|null $stateCode Only available when country code is 'US'
     * @param string|null $countryCode ISO 3166 country code
     * @return Model\Location|Model\Location[]
     */
    public function findLocationByName(string $cityName, int $limit = 1, ?string $stateCode = null, ?string $countryCode = null): Model\Location|array
    {
        $query = implode(',', array_filter([$cityName, $stateCode, $countryCode]));
        $result = $this->getEndpoint('geo.direct')->call(['q' => $query, 'limit' => $limit]);

        if ($limit == 1) {
            return $result[0];
        }

        return $result;
    }

    /**
     * Find geolocation by zip code. Returns `Location` object
     * @param string $zipCode
     * @param string $countryCode ISO 3166 country code
     * @return Model\Location
     */
    public function findLocationByZipCode(string $zipCode, string $countryCode): Model\Location
    {
        return $this->getEndpoint('geo.zip')->call(['zip' => $zipCode . ',' . $countryCode]);
    }

    /**
     * Find geolocation by coordinates. Returns `Location` object when `limit` is 1 or an array of `Location`
     * @param string $lat Latitude
     * @param string $lon Longitude
     * @param int $limit Response data limit. Default 1.
     * @return Model\Location|Model\Location[]
     */
    public function findLocationByCoords(string $lat, string $lon, int $limit = 1): Model\Location|array
    {
        return $this->getEndpoint('geo.reverse')->call(['lat' => $lat, 'lon' => $lon, 'limit' => $limit]);
    }

    /**
     * Get air pollution by `Location` object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @return Model\AirPollution|null
     */
    public function getAirPollution(\Bejblade\OpenWeather\Model\Location $location): Model\AirPollution|null
    {
        $location->setAirPollution($this->getEndpoint('air.pollution')->callWithLocation($location));
        return $location->getAirPollution();
    }

    /**
     * Get air pollution forecast by `Location` object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @return Model\AirPollution[]
     */
    public function getAirPollutionForecast(\Bejblade\OpenWeather\Model\Location $location): array
    {
        return $this->getEndpoint('air.forecast')->callWithLocation($location);
    }

    /**
     * Get air pollution history by `Location` object
     * @param \Bejblade\OpenWeather\Model\Location $location
     * @param string $start Start date (unix time, UTC time zone)
     * @param string $end End date (unix time, UTC time zone)
     * @return Model\AirPollution[]
     */
    public function getAirPollutionHistory(\Bejblade\OpenWeather\Model\Location $location, string $start, string $end): array
    {
        return $this->getEndpoint('air.history')->callWithLocation($location);
    }
}
