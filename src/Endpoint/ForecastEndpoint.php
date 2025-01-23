<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Forecast;

/**
 * Forecast endpoint. Fetch 5 day forecast data for any location
 */
class ForecastEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    /**
     * Number of forecasts which will be returned in the API response. Default 40
     * @var int
     */
    protected int $count = 40;

    /**
     * @param array{lat:string, lon:string, cnt:int} $params Parameters to use in call
     * - `lat` - Required. Latitude
     * - `lon` - Required. Longitude
     * - `cnt` - Number of forecasts which will be returned in the API response. Default 40 (5 days)
     *
     * @return Forecast
     */
    public function call(array $params = []): Forecast
    {
        $params['units'] = $this->units;

        if (!isset($params['cnt'])) {
            $params['cnt'] = $this->count;
        }

        $response = $this->getResponse($params);
        $response['list'] = $this->parseResponseData($response['list']);
        return new Forecast($response['list'], $response['city']['timezone']);
    }

    /**
     * Make a call to API endpoint using Location model
     *
     * @param \Bejblade\OpenWeather\Model\Location $location Location for which weather data will be fetched
     * @param array $params Parameters to use in call
     * - cnt - Number of forecasts which will be returned in the API response
     *
     * @return Forecast
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $params = []): Forecast
    {
        $params = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $params);
        return $this->call($params);
    }

    public function getEndpoint(): string
    {
        return 'forecast';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'cnt', 'units'];
    }

    /**
     * @param array $params Parameters to validate
     * @throws \InvalidArgumentException Thrown when required parameters are missing
     * @return void
     */
    protected function validate(array $params): void
    {
        parent::validate($params);

        if ((!isset($params['lat']) || !isset($params['lon']))) {
            throw new \InvalidArgumentException('Latitude and longitude parameters are required');
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
