<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\OneCall;

use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;

/**
 * weather overview with a human-readable weather summary for today and tomorrow's forecast, utilizing OpenWeather AI technologies.
 */
class WeatherOverviewOneCallEndpoint extends OneCallEndpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:int, date:string} $params Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     * - `date` - The date of weather summary in the YYYY-MM-DD format. Data is available for today and tomorrow. If not specified, the current date will be used by default. Please note that the date is determined by the timezone relevant to the coordinates specified in the API request
     *
     * @return string
     */
    public function call(array $params = []): string
    {
        $params['units'] = $this->units;

        $response = $this->getResponse($params);

        return $response['weather_overview'];
    }

    /**
     * @param array{date:string} $params Parameters to use in call
     * - `date` - The date of weather summary in the YYYY-MM-DD format. Data is available for today and tomorrow. If not specified, the current date will be used by default. Please note that the date is determined by the timezone relevant to the coordinates specified in the API request
     *
     * @return string
     */
    public function callWithLocation(\Bejblade\OpenWeather\Entity\Location $location, array $params = []): string
    {
        $params = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $params);
        return $this->call($params);
    }

    public function getEndpoint(): string
    {
        return parent::getEndpoint() . '/overview';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'date', 'units'];
    }

    /**
     * @param array $params Parameters to validate
     * @throws \InvalidArgumentException Thrown when required parameters are missing
     * @return void
     */
    protected function validate(array $params): void
    {
        parent::validate($params);

        if ((!isset($params['lat']) || !isset($params['lon']))) {
            throw new \InvalidArgumentException('Latitude and longitude parameters are required');
        }
    }

}
