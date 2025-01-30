<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather;

use Bejblade\OpenWeather\Interface\EndpointInterface;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\OpenWeatherClient;

/**
 * OpenWeather API
 *
 * This class is used to interact with OpenWeather API
 */
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
     * Initialize OpenWeather API
     *
     * Set configuration, client and register endpoints
     * @param array $config Array with configuration parameters for Config class.
     *
     * {@see Config::configuration}
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
     * @param string $name Endpoint name
     * @return EndpointInterface|LocationAwareEndpointInterface
     */
    protected function getEndpoint(string $name): EndpointInterface|LocationAwareEndpointInterface
    {
        return $this->endpointRegistry[$name];
    }

    /**
     * Get weather data by location
     * @param Entity\Location $location Location for which weather data will be fetched
     * @return Entity\Weather
     */
    public function getWeather(Entity\Location $location): Entity\Weather|null
    {
        if (!$location->hasWeather() || $location->weather()->isUpdateAvailable()) {
            $location->setWeather($this->getEndpoint('weather')->callWithLocation($location));
        }

        return $location->weather();
    }

    /**
     * Get 5 day forecast for location
     *
     * Each day has 8 forecasts (every 3 hours)
     *
     * @param Entity\Location $location Location for which forecast data will be fetched
     * @return Entity\Forecast|null
     */
    public function getForecast(Entity\Location $location): Entity\Forecast|null
    {
        $location->setForecast($this->getEndpoint('forecast')->callWithLocation($location));
        return $location->forecast();
    }

    /**
     * Get all data for location (weather, forecast and air pollution)
     * @param Entity\Location $location Location for which weather and forecast data will be fetched
     * @return Entity\Location
     */
    public function getAllData(Entity\Location $location): Entity\Location
    {
        $this->getWeather($location);
        $this->getForecast($location);
        $this->getAirPollution($location);
        return $location;
    }

    /**
     * Find geolocation by location name
     *
     * Returns `Location` when `limit` is 1 or an array of `Location`
     *
     * @param string $cityName Name of the city to find
     * @param int $limit Response data limit. Default 1.
     * @param string|null $stateCode Only available when country code is 'US'
     * @param string|null $countryCode ISO 3166 country code
     *
     * @return Entity\Location|Entity\Location[]
     */
    public function findLocationByName(string $cityName, int $limit = 1, ?string $stateCode = null, ?string $countryCode = null): Entity\Location|array
    {
        $query = implode(',', array_filter([$cityName, $stateCode, $countryCode]));
        $result = $this->getEndpoint('geo.direct')->call(['q' => $query, 'limit' => $limit]);

        if ($limit == 1) {
            return $result[0];
        }

        return $result;
    }

    /**
     * Find geolocation by zip code
     * @param string $zipCode Zip code of the location
     * @param string $countryCode ISO 3166 country code
     * @return Entity\Location
     */
    public function findLocationByZipCode(string $zipCode, string $countryCode): Entity\Location
    {
        return $this->getEndpoint('geo.zip')->call(['zip' => $zipCode . ',' . $countryCode]);
    }

    /**
     * Find geolocation by coordinates
     *
     * Returns `Location` when `limit` is 1 or an array of `Location`
     *
     * @param string $lat Latitude
     * @param string $lon Longitude
     * @param int $limit Response data limit. Default 1.
     *
     * @return Entity\Location|Entity\Location[]
     */
    public function findLocationByCoords(string $lat, string $lon, int $limit = 1): Entity\Location|array
    {
        $result = $this->getEndpoint('geo.reverse')->call(['lat' => $lat, 'lon' => $lon, 'limit' => $limit]);
        
        if ($limit == 1) {
            return $result[0];
        }

        return $result;
    }

    /**
     * Get air pollution by location
     * @param Entity\Location $location Location for which air pollution data will be fetched
     * @return Entity\AirPollution|null
     */
    public function getAirPollution(Entity\Location $location): Entity\AirPollution|null
    {
        $location->setAirPollution($this->getEndpoint('air.pollution')->callWithLocation($location));
        return $location->airPollution();
    }

    /**
     * Get air pollution forecast by location
     * @param Entity\Location $location Location for which air pollution data will be fetched
     * @return Entity\AirPollution[]
     */
    public function getAirPollutionForecast(Entity\Location $location): array
    {
        return $this->getEndpoint('air.forecast')->callWithLocation($location);
    }

    /**
     * Get air pollution history by location
     * @param Entity\Location $location Location for which air pollution data will be fetched
     * @param string $start Start date (unix time, UTC time zone)
     * @param string $end End date (unix time, UTC time zone)
     * @return Entity\AirPollution[]
     */
    public function getAirPollutionHistory(Entity\Location $location, string $start, string $end): array
    {
        return $this->getEndpoint('air.history')->callWithLocation($location, ['start' => $start, 'end' => $end]);
    }

    /**
     * Get current weather and 8 days forecast by location using One Call API
     * @param \Bejblade\OpenWeather\Entity\Location $location Location for which weather and forecast data will be fetched
     * @return array{current:Entity\Weather, daily:Entity\Forecast[]}
     */
    public function getOneCallWeatherAndForecast(Entity\Location $location): array
    {
        $result = $this->getEndpoint('onecall')->callWithLocation($location, ['exclude' => 'minutely,hourly,alerts']);
        $location->setWeather($result['current']);
        $location->setForecast($result['daily']);

        return $result;
    }

    /**
     * Get all data using One Call API
     * @param \Bejblade\OpenWeather\Entity\Location $location Location for which data will be fetched
     * @return array{current:Entity\Weather, daily:Entity\Forecast[], hourly:Entity\Forecast[], minutely:array, alerts:array}
     */
    public function getOneCallAllData(Entity\Location $location): array
    {
        return $this->getEndpoint('onecall')->callWithLocation($location);
    }

    /**
     * Get specific data using One Call API
     * @param \Bejblade\OpenWeather\Entity\Location $location Location for which data will be fetched
     * @param string $data Type of data to fetch. Available options: `current`, `minutely`, `hourly,` `daily`, `alerts`
     * @throws \InvalidArgumentException Thrown when invalid data option is provided
     * @return Entity\Weather|Entity\Location|array|null
     */
    public function getOneCallData(Entity\Location $location, string $data): Entity\Weather|Entity\Location|array|null
    {
        $availableOptions = ['current', 'daily', 'hourly', 'minutely', 'alerts'];
        if (!in_array($data, $availableOptions)) {
            throw new \InvalidArgumentException('Invalid data option. Available options: ' . implode(', ', $availableOptions));
        }

        $exclude = array_diff($availableOptions, [$data]);

        $result = $this->getEndpoint('onecall')->callWithLocation($location, ['exclude' => implode(',', $exclude)]);

        if (!isset($result[$data])) {
            return null;
        }

        return $result[$data];
    }

    /**
     * Get specific data using One Call API excluding some of it
     * @param \Bejblade\OpenWeather\Entity\Location $location Location for which data will be fetched
     * @param string $exclude Data to exclude. Available options: `current`, `minutely`, `hourly,` `daily`, `alerts`
     * @return array
     */
    public function getOneCallDataExcept(Entity\Location $location, string $exclude): array
    {
        return $this->getEndpoint('onecall')->callWithLocation($location, ['exclude' => $exclude]);
    }

    /**
     * Get aggregated weather data using One Call API
     * @param \Bejblade\OpenWeather\Entity\Location $location Location for which weather data will be fetched
     * @param string $date Date in the `YYYY-MM-DD` format for which data is requested. Date is available for 46+ years archive (starting from 1979-01-02) up to the 1,5 years ahead forecast to the current date
     * @return Entity\Weather
     */
    public function getWeatherDailyAggregation(Entity\Location $location, string $date): Entity\Weather
    {
        return $this->getEndpoint('onecall.aggregation')->callWithLocation($location, ['date' => $date]);
    }

    /**
     * Get weather data for any timestamp from 1st January 1979 till 4 days ahead forecast using One Call API
     * @param \Bejblade\OpenWeather\Entity\Location $location Location for which weather data will be fetched
     * @param string $timestamp Timestamp in the `UNIX` format for which data is requested. Timestamp is available for 1st January 1979 till 4 days ahead forecast
     * @return Entity\Weather
     */
    public function getWeatherTimeMachine(Entity\Location $location, string $timestamp): Entity\Weather
    {
        return $this->getEndpoint('onecall.timemachine')->callWithLocation($location, ['dt' => $timestamp]);
    }

    /**
     * Get weather overview using One Call API
     * @param Entity\Location $location Location for which weather data will be fetched
     * @param string $date The date of weather summary in YYYY-MM-DD format. Data is available for today and tomorrow. If not specified, the current date will be used by default. Please note that the date is determined by the timezone relevant to the coordinates specified in the API request
     * @return string
     */
    public function getWeatherOverview(Entity\Location $location, string $date = ''): string
    {
        return $this->getEndpoint('onecall.overview')->callWithLocation($location, ['date' => $date]);
    }
}
