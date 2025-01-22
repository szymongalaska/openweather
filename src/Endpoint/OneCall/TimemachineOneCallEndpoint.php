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
     * @param array{lat:string, lon:int, dt:string} $options Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     * - `dt` - Timestamp (Unix time, UTC time zone). Data is available from January 1st, 1979 till 4 days ahead.
     *
     * @return Weather
     */
    public function call(array $options = []): Weather
    {
        $options['units'] = $this->units;

        $response = $this->getResponse($options);

        return $this->parseResponseData($response['data'][0]);
    }

    /**
     * @param array{dt:string} $options Parameters to use in call
     * - `dt` - Timestamp (Unix time, UTC time zone). Data is available from January 1st, 1979 till 4 days ahead.
     *
     * @return Weather
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = []): Weather
    {
        $options = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $options);
        return $this->call($options);
    }

    public function getEndpoint(): string
    {
        return parent::getEndpoint() . '/timemachine';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'dt', 'units'];
    }

    protected function validate(array $options): void
    {
        parent::validate($options);

        if ((!isset($options['lat']) || !isset($options['lon']))) {
            throw new \InvalidArgumentException('Missing latitude and/or longitude parameter');
        }

        if (!isset($options['dt'])) {
            throw new \InvalidArgumentException('Timestamp parameter missing');
        }
    }

    /**
     * Parse response data to `Weather` object
     * @param array $data Response data to parse
     * @return Weather
     */
    private function parseResponseData(array $data): Weather
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

        return new Weather($data);
    }

}
