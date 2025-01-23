<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\Geocoding;

use Bejblade\OpenWeather\Model\Location;

/**
 * Reverse Geocoding endpoint. Fetch Location data by latitude and longitute
 */
class GeocodingReverseEndpoint extends GeocodingEndpoint
{
    /**
     * @param array{lat:int, lon:int, limit:int} $params Parameters to use in call
     * - `lat` - Required. Latitude
     * - `lon` - Required. Longitude
     * - `limit` - Number of locations to retrieve
     *
     * @return Location[]
     */
    public function call(array $params = []): array
    {
        $response = $this->getResponse($params);

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
        return parent::buildUrl() . $this->getEndpoint();
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'limit'];
    }

    /**
     * @param array $params Parameters to validate
     * @throws \InvalidArgumentException Thrown when required parameters are missing
     * @return void
     */
    protected function validate(array $params): void
    {
        parent::validate($params);

        if (!isset($params['lat']) || !isset($params['lat'])) {
            throw new \InvalidArgumentException('Latitude and longitude parameters are required');
        }
    }
}
