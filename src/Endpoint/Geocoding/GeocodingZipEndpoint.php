<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\Geocoding;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Model\Location;

/**
 * Zip Geocoding endpoint. Fetch Location data by zip code, country code
 */
final class GeocodingZipEndpoint extends Endpoint
{
    /**
     * @return Location
     */
    public function call(array $options = [])
    {
        $response = $this->getResponse($options);

        return new Location($response);
    }

    public function getEndpoint(): string
    {
        return 'zip';
    }

    protected function buildUrl(): string
    {
        return 'geo' . '/' . $this->api_version . '/' . $this->getEndpoint();
    }

    protected function getAvailableOptions(): array
    {
        return ['zip'];
    }

    protected function validate(array $options): void
    {
        parent::validate($options);

        if (!isset($options['zip'])) {
            throw new \InvalidArgumentException();
        }
    }
}
