<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\OneCall;

use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Model\Weather;
use Bejblade\OpenWeather\Model\Collection\WeatherCollection;

/**
 * Get current weather, minute forecast for 1 hour, hourly forecast for 48 hours, daily forecast for 8 days and government weather alerts data in with just one call
 */
class WeatherAndForecastOneCallEndpoint extends OneCallEndpoint implements LocationAwareEndpointInterface
{
    /**
     * @param array{lat:string, lon:int, exclude:string} $options Parameters to use in call
     * - `lat` - Latitude
     * - `lon` - Longitude
     * - `exclude` - By using this parameter you can exclude some parts of the weather data from the API response. It should be a comma-delimited list (without spaces).
     *    - `current` - Exclude current weather data
     *    - `minutely` - Exclude minute forecast for 1 hour
     *    - `hourly` - Exclude hourly forecast for 48 hours
     *    - `daily` - Exclude daily forecast for 8 days
     *    - `alerts` - Exclude National weather alerts data from major national weather warning systems
     * @return array
     */
    public function call(array $options = []): array
    {
        $options['units'] = $this->units;

        $response = $this->getResponse($options);

        return $this->parseResponseData($response);
    }

    /**
     * @param array{exclude:string} $options
     * - `exclude` - By using this parameter you can exclude some parts of the weather data from the API response. It should be a comma-delimited list (without spaces).
     *    - `current` - Exclude current weather data
     *    - `minutely` - Exclude minute forecast for 1 hour
     *    - `hourly` - Exclude hourly forecast for 48 hours
     *    - `daily` - Exclude daily forecast for 8 days
     *    - `alerts` - Exclude National weather alerts data from major national weather warning systems
     *
     * @return array
     */
    public function callWithLocation(\Bejblade\OpenWeather\Model\Location $location, array $options = []): array
    {
        $options = array_merge(['lat' => $location->getLatitude(), 'lon' => $location->getLongitude()], $options);
        return $this->call($options);
    }

    protected function getAvailableOptions(): array
    {
        return ['lat', 'lon', 'exclude', 'units'];
    }

    protected function validate(array $options): void
    {
        parent::validate($options);

        if ((!isset($options['lat']) || !isset($options['lon']))) {
            throw new \InvalidArgumentException('Missing latitude and/or longitude parameter');
        }
    }

    /**
     * Parse response data to array with data as objects
     * @param array $data Response data to parse
     * @return array
     */
    private function parseResponseData(array $data): array
    {

        if (isset($data['current'])) {
            $data['current']['temperature'] = [
                'temp' => $data['current']['temp'] ?? null,
                'feels_like' => $data['current']['feels_like'] ?? null,
            ];
            $data['current']['wind'] = [
                'speed' => $data['current']['wind_speed'] ?? null,
                'deg' => $data['current']['wind_deg'] ?? null,
                'gust' => $data['current']['gust'] ?? null,
            ];
            $data['current']['main'] = [
                'pressure' => $data['current']['pressure'] ?? null,
                'humidity' => $data['current']['humidity'] ?? null,
            ];

            $data['current']['clouds'] = ['all' => $data['current']['clouds'] ?? null];

            $parsedData['current'] = new Weather($data['current']);
        }

        if (isset($data['minutely'])) {
            $parsedData['minutely'] = $data['minutely'];
        }

        if (isset($data['hourly'])) {
            $data['hourly'] = array_map(function ($row) {
                $row['temperature'] = [
                    'temp' => $row['temp'] ?? null,
                    'feels_like' => $row['feels_like'] ?? null,
                ];
                $row['wind'] = [
                    'speed' => $row['wind_speed'] ?? null,
                    'deg' => $row['wind_deg'] ?? null,
                    'gust' => $row['gust'] ?? null,
                ];
                $row['main'] = [
                    'pressure' => $row['pressure'] ?? null,
                    'humidity' => $row['humidity'] ?? null,
                ];
                $row['clouds'] = ['all' => $row['clouds'] ?? null];

                return $row;
            }, $data['hourly']);

            $parsedData['hourly'] = new WeatherCollection($data['hourly']);
        }

        if (isset($data['daily'])) {
            $data['daily'] = array_map(function ($row) {
                $row['temperature'] = $row['temp'] ?? [];
                $row['temperature']['afternoon'] = $row['temp']['day'] ?? null;
                $row['temperature']['morning'] = $row['temp']['morn'] ?? null;
                $row['temperature']['evening'] = $row['temp']['eve'] ?? null;
                $row['temperature']['feels_like'] = $row['feels_like'] ?? [];
                $row['temperature']['feels_like']['afternoon'] = $row['feels_like']['day'] ?? null;
                $row['temperature']['feels_like']['morning'] = $row['feels_like']['morn'] ?? null;
                $row['temperature']['feels_like']['evening'] = $row['feels_like']['eve'] ?? null;
                $row['wind'] = [
                    'speed' => $row['wind_speed'] ?? null,
                    'deg' => $row['wind_deg'] ?? null,
                    'gust' => $row['gust'] ?? null,
                ];
                $row['main'] = [
                    'pressure' => $row['pressure'] ?? null,
                    'humidity' => $row['humidity'] ?? null,
                ];
                $row['clouds'] = ['all' => $row['clouds'] ?? null];
                $row['rain'] = ['1h' => $row['rain'] ?? null];
                $row['snow'] = ['1h' => $row['snow'] ?? null];

                return $row;
            }, $data['daily']);

            $parsedData['daily'] = new WeatherCollection($data['daily']);
        }

        return $parsedData;
    }

}
