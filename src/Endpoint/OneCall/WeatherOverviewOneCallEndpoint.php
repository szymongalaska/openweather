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
     * @param array{lat:string, lon:int, date:string} $options Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     * - `date` - The date the user wants to get a weather summary in the YYYY-MM-DD format. Data is available for today and tomorrow. If not specified, the current date will be used by default. Please note that the date is determined by the timezone relevant to the coordinates specified in the API request
     *
     * @return
     */
    public function call(array $options = []): string
    {
        $options['units'] = $this->units;

        $response = $this->getResponse($options);

        return $response['weather_overview'];
    }

    /**
     * @param array{date:string} $options Parameters to use in call
     * - `date` - The date the user wants to get a weather summary in the YYYY-MM-DD format. Data is available for today and tomorrow. If not specified, the current date will be used by default. Please note that the date is determined by the timezone relevant to the coordinates specified in the API request
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = [])
    {
        $options = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $options);
        return $this->call($options);
    }


    public function getEndpoint(): string
    {
        return parent::getEndpoint().'/overview';
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'date', 'units'];
    }

    protected function validate(array $options): void
    {
        parent::validate($options);

        if ((!isset($options['lat']) || !isset($options['lon']))) {
            throw new \InvalidArgumentException('Missing latitude and/or longitude parameter');
        }
    }

}
