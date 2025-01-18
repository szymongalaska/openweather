<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

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
    public float $feels_like;

    /**
     * Minimum temperature observed within large megalopolises and urban areas
     * @var float
     */
    public float $temperature_min;

    /**
     * Maximum temperature observed within large megalopolises and urban areas
     * @var float
     */
    public float $temperature_max;

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
    private \DateTime $last_updated;

    /**
     * Date format applied to `$last_updated`
     * @var string|null
     */
    private string $date_format;

    /**
     * Units in which some of parameters are formatted
     * - standard - Kelvin | meter/sec
     * - metric - Celsius | meter/sec
     * - imperial - Fahrenheit | miles/hour
     * @var string
     */
    public string $units;


    public function __construct(array $data, string $units, ?string $date_format = null, ?string $timezone = null)
    {
        $this->weather = $data['weather'][0]['main'];
        $this->description = $data['weather'][0]['description'];
        $this->icon = $data['weather'][0]['icon'];
        $this->temperature = $data['main']['temp'];
        $this->feels_like = $data['main']['feels_like'];
        $this->temperature_min = $data['main']['temp_min'];
        $this->temperature_max = $data['main']['temp_max'];
        $this->pressure = $data['main']['pressure'];
        $this->humidity = $data['main']['humidity'];
        $this->visibility = $data['visibility'];
        $this->wind = new Wind($data['wind']);
        $this->clouds = $data['clouds']['all'];
        $this->rain = $data['rain']['1h'] ?? null;
        $this->snow = $data['snow']['1h'] ?? null;

        $this->last_updated = new \DateTime('@' . $data['dt']);

        if ($timezone) {
            $this->last_updated->setTimezone(new \DateTimeZone($timezone));
        }

        $this->date_format = $date_format ?? 'd/m/Y H:i';

        $this->units = $units;

    }

    /**
     * Get formatted date and time of last data calculation
     * @return string
     */
    public function getLastUpdateTime(): string
    {
        return $this->last_updated->format($this->date_format);
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
        // Calculate difference in minutes
        $diff = abs((new \DateTime('now', $this->last_updated->getTimezone()))->getTimestamp() - $this->last_updated->getTimestamp()) * 60;

        if ($diff >= 10) {
            return true;
        }

        return false;
    }
}
