<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

use Bejblade\OpenWeather\Config;

class Weather
{
    /**
     * Weather name
     * @var string
     */
    public string $weather;

    /**
     * Weather description
     * @var string
     */
    public string $description;

    /**
     * Weather icon id
     * @var string
     */
    public string $icon;

    /**
     * Current temperature
     * @var float
     */
    public float $temperature;

    /**
     * Human perception of current temperature
     * @var float
     */
    public float $feelsLike;

    /**
     * Minimum temperature observed within large megalopolises and urban areas
     * @var float
     */
    public float $temperatureMin;

    /**
     * Maximum temperature observed within large megalopolises and urban areas
     * @var float
     */
    public float $temperatureMax;

    /**
     * Atmospheric pressure on the sea level in hPa
     * @var int
     */
    public int $pressure;

    /**
     * Humidity in %
     * @var int
     */
    public int $humidity;

    /**
     * Visibility in meters, maximum is 10km
     * @var int
     */
    public int $visibility;

    /**
     * Wind data of current weather
     * @var Wind
     */
    public Wind $wind;

    /**
     * Cloudiness in %
     * @var int
     */
    public int $clouds;

    /**
     * Precipitation of rain in mm/h
     * @var int|null
     */
    public ?float $rain;

    /**
     * Precipitation of snow in mm/h
     * @var float|null
     */
    public ?float $snow;

    /**
     * Date and time of last data calculation
     * @var \DateTime
     */
    private \DateTime $lastUpdated;

    /**
     * Date format applied to `$lastUpdated`
     * @var string
     */
    private string $dateFormat;

    /**
     * Units in which some of parameters are formatted
     * - standard - Kelvin | meter/sec
     * - metric - Celsius | meter/sec
     * - imperial - Fahrenheit | miles/hour
     * @var string
     */
    public string $units;


    public function __construct(array $data)
    {
        $this->weather = $data['weather'][0]['main'];
        $this->description = $data['weather'][0]['description'];
        $this->icon = $data['weather'][0]['icon'];
        $this->temperature = $data['main']['temp'];
        $this->feelsLike = $data['main']['feels_like'];
        $this->temperatureMin = $data['main']['temp_min'];
        $this->temperatureMax = $data['main']['temp_max'];
        $this->pressure = $data['main']['pressure'];
        $this->humidity = $data['main']['humidity'];
        $this->visibility = $data['visibility'];
        $this->wind = new Wind($data['wind']);
        $this->clouds = $data['clouds']['all'];
        $this->rain = $data['rain']['1h'] ?? null;
        $this->snow = $data['snow']['1h'] ?? null;
        $this->lastUpdated = new \DateTime('@' . $data['dt']);
        $this->lastUpdated->setTimezone(new \DateTimeZone(Config::configuration()->get('timezone')));
        $this->dateFormat = $this->getDateFormat();
        $this->units = Config::configuration()->get('units');

    }

    /**
     * Get formatted date and time of last data calculation
     * @return string
     */
    public function getLastUpdateTime(): string
    {
        return $this->lastUpdated->format($this->dateFormat);
    }

    /**
     * Check if there is any rain
     * @return bool
     */
    public function isRaining(): bool
    {
        return $this->rain > 0;
    }

    /**
     * Check if there is any snow
     * @return bool
     */
    public function isSnowing(): bool
    {
        return $this->snow > 0;
    }

    /**
     * Weather data is updated every 10 minutes, check if 10 minutes passed since last fetch.
     * @return bool
     */
    public function isUpdateAvailable(): bool
    {
        $diff = abs((new \DateTime('now', $this->lastUpdated->getTimezone()))->getTimestamp() - $this->lastUpdated->getTimestamp());

        return $diff >= 600; // 10 minutes
    }

    private function getDateFormat(): string
    {
        return $this->dateFormat = preg_replace('/d|D|j|l|N|S|w|z/', Config::configuration()->get('day_format'), Config::configuration()->get('date_format') . ' ' . Config::configuration()->get('time_format'));
    }
}
