<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\Geocoding;

use Bejblade\OpenWeather\Entity\Location;

/**
 * Zip Geocoding endpoint. Fetch Location data by zip code, country code
 */
class GeocodingZipEndpoint extends GeocodingEndpoint
{
    /**
     * @param array{zip:string} $params Parameters to use in call
     * - `zip` - required and needs to be in this format `zip_code, country_code`
     *
     * @return Location
     */
    public function call(array $params = []): Location
    {
        $response = $this->getResponse($params);

        return new Location($response);
    }

    public function getEndpoint(): string
    {
        return 'zip';
    }

    protected function buildUrl(): string
    {
        return parent::buildUrl() . $this->getEndpoint();
    }

    protected function getAvailableOptions(): array
    {
        return ['zip'];
    }

    /**
     * @param array $params Parameters to validate
     * @throws \InvalidArgumentException Thrown when required parameters are missing
     * @return void
     */
    protected function validate(array $params): void
    {
        parent::validate($params);

        if (!isset($params['zip'])) {
            throw new \InvalidArgumentException('Zip parameter is required');
        }
    }
}
