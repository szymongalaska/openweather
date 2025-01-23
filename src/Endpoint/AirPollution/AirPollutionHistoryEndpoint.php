<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\AirPollution;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Entity\AirPollution;

/**
 * Air pollution history endpoint. Get historical air pollution data for any coordinates on the globe (accessible from 27th November 2020).
 */
class AirPollutionHistoryEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:int, start:string, end:string} $params Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     * - `start` - Start date (unix time, UTC time zone)
     * - `end` - End date (unix time, UTC time zone)
     *
     * @return AirPollution[]
     */
    public function call(array $params = []): array
    {
        $response = $this->getResponse($params);

        return $this->convertResponseToAirPollutionArray($response['list']);
    }

    /**
     * Convert Air Pollution Forecast response to AirPollution list
     * @param array $response Array of air pollution data
     * @return \Bejblade\OpenWeather\Entity\AirPollution[]
     */
    private function convertResponseToAirPollutionArray(array $airPollutionList): array
    {
        $airPollutionList = array_map(function ($airPollution) {
            return new AirPollution($airPollution);
        }, $airPollutionList);

        return $airPollutionList;
    }

    /**
     * @param array $params Parameters to use in call
     * - `start` - Start date (unix time, UTC time zone)
     * - `end` - End date (unix time, UTC time zone)
     *
     * @return AirPollution[]
     */
    public function callWithLocation(\Bejblade\OpenWeather\Entity\Location $location, array $params = []): array
    {
        $params = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $params);
        return $this->call($params);
    }

    public function getEndpoint(): string
    {
        return 'air_pollution/history';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'start', 'end'];
    }

    protected function validate(array $params): void
    {
        parent::validate($params);

        if ((!isset($params['lat']) || !isset($params['lon']))) {
            throw new \InvalidArgumentException('Latitude and longitude parameters are required');
        }

        if (!isset($params['start']) || !isset($params['end'])) {
            throw new \InvalidArgumentException('Missing date range');
        }
    }

}
