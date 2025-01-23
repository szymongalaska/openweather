<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Weather;

/**
 * Weather endpoint. Fetch current weather data by `Location` or latitude and longitude
 */
class WeatherEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:string} $params Parameters to use in call
     * - `lat` - Latitude of location (required)
     * - `lon` - Longitude of location (required)
     *
     * @return Weather
     */
    public function call(array $params = [])
    {
        $params['units'] = $this->units;

        $response = $this->getResponse($params);
        $response = $this->parseResponseData($response);

        return new Weather($response, $response['timezone']);
    }

    /**
     * Make a call to API endpoint using Location model
     * @param \Bejblade\OpenWeather\Model\Location $location Location for which weather data will be fetched
     * @param array $params Not used in this endpoint
     * @return Weather
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $params = []): Weather
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
