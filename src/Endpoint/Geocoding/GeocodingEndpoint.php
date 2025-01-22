<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\Geocoding;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Config;

/**
 *
 */
abstract class GeocodingEndpoint extends Endpoint
{
    protected function buildUrl(): string
    {
        return 'geo' . '/' . $this->apiVersion . '/';
    }

    protected function configure(): void
    {
        parent::configure();
        $this->apiVersion = Config::configuration()->get('geo_api_version');
    }
}
