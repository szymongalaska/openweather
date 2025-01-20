<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\Geocoding;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Model\Location;
use Bejblade\OpenWeather\Config;

/**
 * Zip Geocoding endpoint. Fetch Location data by zip code, country code
 */
class GeocodingZipEndpoint extends Endpoint
{
    /**
     * @param array{zip:string} $options Parameters to use in call
     * - `zip` - required and needs to be in this format `zip_code, country_code`
     *
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
        return 'geo' . '/' . $this->apiVersion . '/' . $this->getEndpoint();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->apiVersion = Config::configuration()->get('geo_api_version');
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
