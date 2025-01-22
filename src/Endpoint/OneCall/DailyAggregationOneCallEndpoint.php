<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\OneCall;

use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Weather;

/**
 * Get aggregated weather data for a particular date from 2nd January 1979 till long-term forecast for 1,5 years ahead
 */
class DailyAggregationOneCallEndpoint extends OneCallEndpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:int, date:string} $options Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     * - `date` - Date in the `YYYY-MM-DD` format for which data is requested. Date is available for 46+ years archive (starting from 1979-01-02) up to the 1,5 years ahead forecast to the current date
     *
     * @return Weather
     */
    public function call(array $options = []): Weather
    {
        $options['units'] = $this->units;

        $response = $this->getResponse($options);

        return $this->parseResponseData($response);
    }

    /**
     * @param array{date:string} $options Parameters to use in call
     * - `date` - Date in the `YYYY-MM-DD` format for which data is requested. Date is available for 46+ years archive (starting from 1979-01-02) up to the 1,5 years ahead forecast to the current date
     *
     * @return Weather
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = []): Weather
    {
        $options = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $options);
        return $this->call($options);
    }

    public function getEndpoint(): string
    {
        return parent::getEndpoint() . '/day_summary';
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

        if (!isset($options['date'])) {
            throw new \InvalidArgumentException('Date parameter missing');
        }
    }

    /**
     * Parse response data to `Weather` object
     * @param array $data Response data to parse
     * @return Weather
     */
    private function parseResponseData(array $data): Weather
    {
        $data['wind']['max']['deg'] = $data['wind']['max']['direction'] ?? null;

        $data = [
            'dt' => (new \DateTime($data['date']))->getTimestamp(),
            'clouds' => $data['cloud_cover']['afternoon'] ?? null,
            'humidity' => $data['humidity']['afternoon'] ?? null,
            'rain' => ['1h' => $data['precipitation']['total']] ?? null,
            'pressure' => $data['pressure']['afternoon'] ?? null,
            'wind' => $data['wind']['max'] ?? null,
            'temperature' => $data['temperature'],
        ];
        return new Weather($data);
    }

}
