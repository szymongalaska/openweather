<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\OneCall;

use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Entity\Weather;

/**
 * Get aggregated weather data for a particular date from 2nd January 1979 till long-term forecast for 1,5 years ahead
 */
class DailyAggregationOneCallEndpoint extends OneCallEndpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:int, date:string} $params Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     * - `date` - Date in the `YYYY-MM-DD` format for which data is requested. Date is available for 46+ years archive (starting from 1979-01-02) up to the 1,5 years ahead forecast to the current date
     *
     * @return Weather
     */
    public function call(array $params = []): Weather
    {
        $params['units'] = $this->units;

        $response = $this->getResponse($params);

        return $this->parseResponseData($response);
    }

    /**
     * @param array{date:string} $params Parameters to use in call
     * - `date` - Date in the `YYYY-MM-DD` format for which data is requested. Date is available for 46+ years archive (starting from 1979-01-02) up to the 1,5 years ahead forecast to the current date
     *
     * @return Weather
     */
    public function callWithLocation(\Bejblade\OpenWeather\Entity\Location $location, array $params = []): Weather
    {
        $params = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $params);
        return $this->call($params);
    }

    public function getEndpoint(): string
    {
        return parent::getEndpoint() . '/day_summary';
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

        if (!isset($params['date'])) {
            throw new \InvalidArgumentException('Date parameter is required');
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
