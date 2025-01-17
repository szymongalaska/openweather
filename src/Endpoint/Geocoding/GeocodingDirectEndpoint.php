<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\Geocoding;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Model\Location;

/**
 * Direct Geocoding endpoint. Fetch Location data by city name, state code and country code
 */
final class GeocodingDirectEndpoint extends Endpoint
{
    /**
     * @return Location[]
     */
    public function call(array $options = [])
    {
        $response = $this->getResponse($options);

        return array_map(function ($item) {
            return new Location($item);
        }, $response);
    }

    protected function getAvailableOptions(): array
    {
        return ['q', 'limit'];
    }

    public function getEndpoint(): string
    {
        return 'direct';
    }

    protected function buildUrl(): string
    {
        return 'geo' . '/' . $this->api_version . '/' . $this->getEndpoint();
    }

    protected function validate(array $options): void
    {
        parent::validate($options);

        if (!isset($options['q'])) {
            throw new \InvalidArgumentException('Missing q argument');
        }
    }

}
