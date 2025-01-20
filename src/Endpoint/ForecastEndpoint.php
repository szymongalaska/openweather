<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Weather;
use Bejblade\OpenWeather\Model\Forecast;

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
     * @return Forecast
     */
    public function call(array $options = []): Forecast
    {
        $options['units'] = $this->units;

        if (!isset($options['cnt'])) {
            $options['cnt'] = $this->count;
        }

        $response = $this->getResponse($options);
        return $this->convertResponseToForecast($response['list'], $options['units']);
    }

    /**
     * Convert forecast response to Weather list and return Forecast
     * @param array $response Array of forecasts
     * @param string $units Units of measurement which were used to fetch forecast
     * @return \Bejblade\OpenWeather\Model\Forecast
     */
    private function convertResponseToForecast(array $weatherList, string $units): Forecast
    {
        $weatherList = array_map(function ($weather) use ($units) {
            return new Weather($weather);
        }, $weatherList);

        return new Forecast($weatherList);
    }

    /**
     * Make a call to API endpoint using Location model
     *
     * @param \Bejblade\OpenWeather\Model\Location $location Location for which weather data will be fetched
     * @param array $options Parameters to use in call
     * - cnt - Number of forecasts which will be returned in the API response
     *
     * @return Forecast
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = []): Forecast
    {
        $options = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $options);
        return $this->call($options);
    }

    public function getEndpoint(): string
    {
        return 'forecast';
    }

    protected function buildUrl(): string
    {
        return 'data' . '/' . $this->apiVersion . '/' . $this->getEndpoint();
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'cnt'];
    }

    protected function validate(array $options): void
    {
        parent::validate($options);

        if ((!isset($options['lat']) || !isset($options['lon']))) {
            throw new \InvalidArgumentException('Missing latitude and/or longitute parameter');
        }
    }
}
