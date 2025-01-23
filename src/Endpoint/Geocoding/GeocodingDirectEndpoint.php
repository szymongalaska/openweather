<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\Geocoding;

use Bejblade\OpenWeather\Model\Location;
use Bejblade\OpenWeather\Config;

/**
 * Direct Geocoding endpoint. Fetch Location data by city name, state code and country code
 */
class GeocodingDirectEndpoint extends GeocodingEndpoint
{
    /**
     * @param array{q:string, limit:int} $params Parameters to use in call
     * - `q` - required and needs to be in this format `city_name, state_code, country_code` (state_code only available when country_code is 'US')
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
        return 'direct';
    }

    protected function buildUrl(): string
    {
        return parent::buildUrl() . $this->getEndpoint();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->apiVersion = Config::configuration()->get('geo_api_version');
    }

    protected function getAvailableOptions(): array
    {
        return ['q', 'limit'];
    }

    /**
     * @param array $params Parameters to validate
     * @throws \InvalidArgumentException Thrown when required parameters are missing
     * @return void
     */
    protected function validate(array $params): void
    {
        parent::validate($params);

        if (!isset($params['q'])) {
            throw new \InvalidArgumentException('Q parameter is required');
        }
    }

}
