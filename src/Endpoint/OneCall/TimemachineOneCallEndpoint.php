<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\OneCall;

use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Weather;

/**
 * Get weather data for any timestamp from 1st January 1979 till 4 days ahead forecast
 */
class TimemachineOneCallEndpoint extends OneCallEndpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:int, dt:string} $params Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     * - `dt` - Timestamp (Unix time, UTC time zone). Data is available from January 1st, 1979 till 4 days ahead.
     *
     * @return Weather
     */
    public function call(array $params = []): Weather
    {
        $params['units'] = $this->units;

        $response = $this->getResponse($params);

        return $this->parseResponseData($response['data'][0], $response['timezone_offset']);
    }

    /**
     * @param array{dt:string} $params Parameters to use in call
     * - `dt` - Timestamp (Unix time, UTC time zone). Data is available from January 1st, 1979 till 4 days ahead.
     *
     * @return Weather
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $params = []): Weather
    {
        $params = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $params);
        return $this->call($params);
    }

    public function getEndpoint(): string
    {
        return parent::getEndpoint() . '/timemachine';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'dt', 'units'];
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

        if (!isset($params['dt'])) {
            throw new \InvalidArgumentException('Timestamp parameter is required');
        }
    }

    /**
     * Parse response data to `Weather` object
     * @param array $data Response data to parse
     * @return Weather
     */
    private function parseResponseData(array $data, int $timezone): Weather
    {
        $data['temperature'] = [
            'temp' => $data['temp'] ?? null,
            'feels_like' => $data['feels_like'] ?? null,
        ];
        $data['wind'] = [
            'speed' => $data['wind_speed'] ?? null,
            'deg' => $data['wind_deg'] ?? null,
            'gust' => $data['gust'] ?? null,
        ];
        $data['main'] = [
            'pressure' => $data['pressure'] ?? null,
            'humidity' => $data['humidity'] ?? null,
        ];

        $data['clouds'] = ['all' => $data['clouds'] ?? null];

        return new Weather($data, $timezone);
    }

}
