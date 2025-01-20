<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\Geocoding;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Model\Location;
use Bejblade\OpenWeather\Config;

/**
 * Reverse Geocoding endpoint. Fetch Location data by latitude and longitute
 */
class GeocodingReverseEndpoint extends Endpoint
{
    /**
     * @param array{lat:int, lon:int, limit:int} $options Parameters to use in call
     * - `lat` - Required. Latitude
     * - `lon` - Required. Longitude
     * - `limit` - Number of locations to retrieve
     *
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
        return 'geo' . '/' . $this->apiVersion . '/' . $this->getEndpoint();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->apiVersion = Config::configuration()->get('geo_api_version');
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
