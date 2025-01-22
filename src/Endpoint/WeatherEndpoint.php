<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Weather;

/**
 * Weather endpoint. Fetch current weather data by Location or latitude and longitude
 */
class WeatherEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:string} $options Parameters to use in call
     * - `lat` - Required. Latitude
     * - `lon` - Required. Longitude
     *
     * @return Weather
     */
    public function call(array $options = [])
    {
        $options['units'] = $this->units;

        $response = $this->getResponse($options);
        $response = $this->parseResponseData($response);

        return new Weather($response);
    }

    /**
     * Make a call to API endpoint using Location model also weather as Location
     * @param \Bejblade\OpenWeather\Model\Location $location Location for which weather data will be fetched
     * @param array $options Not used in this endpoint
     * @return Weather
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = []): Weather
    {
        return $this->call(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()]);
    }

    public function getEndpoint(): string
    {
        return 'weather';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'units'];
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
    private function parseResponseData(array $data): array
    {
        $data['temperature'] = [
            'temp' => $data['main']['temp'],
            'feels_like' => $data['main']['feels_like'],
            'min' => $data['main']['temp_min'],
            'max' => $data['main']['temp_max']
        ];

        return $data;
    }
}
