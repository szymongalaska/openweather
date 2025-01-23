<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\AirPollution;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Entity\AirPollution;

/**
 * Air pollution endpoint. Get current air pollution data for any coordinates on the globe
 */
class AirPollutionEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:int} $params Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     *
     * @return AirPollution
     */
    public function call(array $params = []): AirPollution
    {
        $response = $this->getResponse($params);

        return new AirPollution($response['list'][0]);
    }

    /**
     * @param array $params Not used in this endpoint
     * @return AirPollution
     */
    public function callWithLocation(\Bejblade\OpenWeather\Entity\Location $location, array $params = []): AirPollution
    {
        $params = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $params);
        return $this->call($params);
    }

    public function getEndpoint(): string
    {
        return 'air_pollution';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon'];
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

}
