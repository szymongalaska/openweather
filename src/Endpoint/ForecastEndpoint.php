<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Weather;
use Bejblade\OpenWeather\Model\Forecast;

/**
 * Forecast endpoint. Fetch 5 day forecast data for any given location wt
 */
class ForecastEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    /**
     * Number of forecasts which will be returned in the API response. Default 40
     * @var int
     */
    protected int $count = 40;

    /**
     * Date format used to create Weather
     * @var string
     */
    protected string $date_format;

    /**
     * Timezone used to create Weather
     * @var string
     */
    protected string $timezone;

    /**
     * Default units to use in calls
     * @var string
     */
    protected string $units;

    /**
     * @param array $options Parameters to use in call
     * - lat - Required. Latitude
     * - lon - Required. Longitude
     * - cnt - Number of forecasts which will be returned in the API response
     * - units - Units of measurement. standard, metric and imperial units are available. If you do not use the units parameter, default Endpoint units will be applied by default.
     * - lang - You can use this parameter to get the output in your language.
     *
     * @return Forecast
     */
    public function call(array $options = []): Forecast
    {
        if (!isset($options['units'])) {
            $options['units'] = $this->units;
        }

        if (!isset($options['cnt'])) {
            $options['cnt'] = $this->count;
        }

        $response = $this->getResponse($options);
        return $this->convertResponseToForecast($response, $options['units']);
    }

    /**
     * Convert forecast response to Weather list and return Forecast
     * @param array $response Array of forecasts
     * @param string $units Units of measurement which were used to fetch forecast
     * @return \Bejblade\OpenWeather\Model\Forecast
     */
    private function convertResponseToForecast(array $response, string $units): Forecast
    {
        $weatherList = array_map(function ($weather) use ($units) {
            return new Weather($weather, $units, $this->date_format, $this->timezone);
        }, $response);

        return new Forecast($weatherList);
    }

    /**
     * Make a call to API endpoint using Location model
     *
     * @param \Bejblade\OpenWeather\Model\Location $location Location for which weather data will be fetched
     * @param array $options Parameters to use in call
     * - units - Units of measurement. standard, metric and imperial units are available. If you do not use the units parameter, default Endpoint units will be applied by default.
     * - lang - You can use this parameter to get the output in your language.
     * - cnt - Number of forecasts which will be returned in the API response
     *
     * @return Forecast
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = []): Forecast
    {
        $options = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $options);
        return $this->call($options);
    }

    public function getEndpoint(): string
    {
        return 'forecast';
    }

    protected function buildUrl(): string
    {
        return 'data' . '/' . $this->api_version . '/' . $this->getEndpoint();
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'units', 'lang', 'cnt'];
    }

    protected function validate(array $options): void
    {
        parent::validate($options);

        if ((!isset($options['lat']) && !isset($options['lon']))) {
            throw new \InvalidArgumentException('Missing Location or latitude and longitute parameter');
        }
    }

    protected function validateConfiguration(array $config): void
    {
        parent::validateConfiguration($config);

        if (empty($config['units'])) {
            throw new \InvalidArgumentException('Missing units format in configuration');
        }

        $this->units = $config['units'];
    }
}
