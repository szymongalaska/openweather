<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\AirPollution;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\AirPollution;

/**
 * Air pollution endpoint. Get forecast (every hour for four days) air pollution data for any coordinates on the globe.
 */
class AirPollutionForecastEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:int} $options Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     *
     * @return AirPollution[]
     */
    public function call(array $options = []): array
    {
        $response = $this->getResponse($options);

        return $this->convertResponseToAirPollutionArray($response['list']);
    }

    /**
     * Convert Air Pollution Forecast response to AirPollution list
     * @param array $response Array of air pollution data
     * @return AirPollution[]
     */
    private function convertResponseToAirPollutionArray(array $airPollutionList): array
    {
        $airPollutionList = array_map(function ($airPollution) {
            return new AirPollution($airPollution);
        }, $airPollutionList);

        return $airPollutionList;
    }

    /**
     * @param array $options Not used in this endpoint
     * @return AirPollution[]
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = []): array
    {
        $options = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $options);
        return $this->call($options);
    }

    public function getEndpoint(): string
    {
        return 'air_pollution/forecast';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon'];
    }

    protected function validate(array $options): void
    {
        parent::validate($options);

        if ((!isset($options['lat']) || !isset($options['lon']))) {
            throw new \InvalidArgumentException('Missing latitude and/or longitude parameter');
        }
    }

}
