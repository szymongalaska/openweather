<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Collection\WeatherCollection;

/**
 * Forecast endpoint. Fetch 5 day forecast data for any given location wt
 */
class ForecastEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    /**
     * Number of forecasts which will be returned in the API response. Default 40
     * @var int
     */
    protected int $count = 40;

    /**
     * @param array{lat:string, lon:string, cnt:int} $options Parameters to use in call
     * - `lat` - Required. Latitude
     * - `lon` - Required. Longitude
     * - `cnt` - Number of forecasts which will be returned in the API response. Default 40 (5 days)
     *
     * @return WeatherCollection
     */
    public function call(array $options = []): WeatherCollection
    {
        $options['units'] = $this->units;

        if (!isset($options['cnt'])) {
            $options['cnt'] = $this->count;
        }

        $response = $this->getResponse($options);
        $response['list'] = $this->parseResponseData($response['list']);
        return new WeatherCollection($response['list']);
    }

    /**
     * Make a call to API endpoint using Location model
     *
     * @param \Bejblade\OpenWeather\Model\Location $location Location for which weather data will be fetched
     * @param array $options Parameters to use in call
     * - cnt - Number of forecasts which will be returned in the API response
     *
     * @return WeatherCollection
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = []): WeatherCollection
    {
        $options = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $options);
        return $this->call($options);
    }

    public function getEndpoint(): string
    {
        return 'forecast';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'cnt', 'units'];
    }

    protected function validate(array $options): void
    {
        parent::validate($options);

        if ((!isset($options['lat']) || !isset($options['lon']))) {
            throw new \InvalidArgumentException('Missing latitude and/or longitude parameter');
        }
    }

    /**
     * Parse response data (group temperature data)
     * @param array $data Response data to parse
     * @return array
     */
    private function parseResponseData(array $data)
    {
        return array_map(function ($row) {
            $row['temperature'] = [
                'temp' => $row['main']['temp'],
                'feels_like' => $row['main']['feels_like'],
                'min' => $row['main']['temp_min'],
                'max' => $row['main']['temp_max']
            ];

            return $row;
        }, $data);
    }
}
