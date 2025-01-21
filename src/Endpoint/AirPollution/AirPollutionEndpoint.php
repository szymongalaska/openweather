<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\AirPollution;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\AirPollution;

/**
 * Air pollution endpoint. Get current air pollution data for any coordinates on the globe
 */
class AirPollutionEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:int} $options Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     *
     * @return AirPollution
     */
    public function call(array $options = [])
    {
        $response = $this->getResponse($options);

        return new AirPollution($response['list'][0]);
    }

    /**
     * @param array $options Not used in this endpoint
     * @return AirPollution
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = []): AirPollution
    {
        $options = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $options);
        return $this->call($options);
    }

    public function getEndpoint(): string
    {
        return 'air_pollution';
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
