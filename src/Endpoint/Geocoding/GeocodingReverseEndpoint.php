<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\Geocoding;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Model\Location;

/**
 * Reverse Geocoding endpoint. Fetch Location data by latitude and longitute
 */
final class GeocodingReverseEndpoint extends Endpoint
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

    public function getEndpoint(): string
    {
        return 'reverse';
    }

    protected function buildUrl(): string
    {
        return 'geo' . '/' . $this->api_version . $this->getEndpoint();
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'limit'];
    }


    protected function validate(array $options): void
    {
        parent::validate($options);

        if (!isset($options['lat']) || !isset($options['lat'])) {
            throw new \InvalidArgumentException();
        }
    }
}
