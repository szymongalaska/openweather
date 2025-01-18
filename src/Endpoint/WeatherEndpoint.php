<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Weather;

/**
 * Weather endpoint. Fetch current weather data by Location or latitude and longitude
 */
class WeatherEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
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
     * - units - Units of measurement. standard, metric and imperial units are available. If you do not use the units parameter, default Endpoint units will be applied by default.
     * - lang - You can use this parameter to get the output in your language.
     *
     * @return Weather
     */
    public function call(array $options = []): Weather
    {
        if (!isset($options['units'])) {
            $options['units'] = $this->units;
        }

        $response = $this->getResponse($options);


        return new Weather($response, $options['units'] ?? $this->units, $this->date_format, $this->timezone);
    }

    /**
     * Make a call to API endpoint using Location model
     * 
     * @param \Bejblade\OpenWeather\Model\Location $location Location for which weather data will be fetched
     * @param array $options Parameters to use in call
     * - units - Units of measurement. standard, metric and imperial units are available. If you do not use the units parameter, default Endpoint units will be applied by default.
     * - lang - You can use this parameter to get the output in your language.
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
        return 'weather';
    }

    protected function buildUrl(): string
    {
        return 'data' . '/' . $this->api_version . '/' . $this->getEndpoint();
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'units', 'lang'];
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

        if (empty($config['date_format']) || empty($config['time_format']) || empty($config['day_format'])) {
            throw new \InvalidArgumentException('Missing date format in configuration');
        }

        if (empty($config['units'])) {
            throw new \InvalidArgumentException('Missing units format in configuration');
        }

        if (empty($config['timezone'])) {
            throw new \InvalidArgumentException('Missing timezone in configuration');
        }

        $this->date_format = preg_replace('/d|D|j|l|N|S|w|z/', $config['day_format'], $config['date_format'] . ' ' . $config['time_format']);
        $this->timezone = $config['timezone'];
        $this->units = $config['units'];
    }

}
