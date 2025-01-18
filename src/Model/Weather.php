<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

class Weather
{
    /**
     * Cloudiness in %
     * @var int
     */
    private int $clouds;

    /**
     * Weather description
     * @var string
     */
    private string $description;

    /**
     * Date format applied to `$last_updated`
     * @var string|null
     */
    private string $date_format;

    /**
     * Human perception of current temperature
     * @var float
     */
    private float $feels_like;

    /**
     * Humidity in %
     * @var int
     */
    private int $humidity;

    /**
     * Weather icon id
     * @var string
     */
    private string $icon;

    /**
     * Date and time of last data calculation
     * @var \DateTime
     */
    private \DateTime $last_updated;

    /**
     * Precipitation of rain in mm/h
     * @var float|null
     */
    private ?float $rain;

    /**
     * Precipitation of snow in mm/h
     * @var float|null
     */
    private ?float $snow;

    /**
     * Current temperature
     * @var float
     */
    private float $temperature;

    /**
     * Maximum temperature observed within large megalopolises and urban areas
     * @var float
     */
    private float $temperature_max;

    /**
     * Minimum temperature observed within large megalopolises and urban areas
     * @var float
     */
    private float $temperature_min;

    /**
     * Atmospheric pressure on the sea level in hPa
     * @var int
     */
    private int $pressure;

    /**
     * Visibility in meters, maximum is 10km
     * @var int
     */
    private int $visibility;

    /**
     * Weather name
     * @var string
     */
    private string $weather;

    /**
     * Wind data of current weather
     * @var Wind
     */
    private ?Wind $wind;

    /**
     * Units in which parameters are formatted
     * @var string
     */
    private string $units;

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
        $this->wind = $data['wind'] ? new Wind($data['wind']) : null;
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
     * Get cloudiness in %
     * @return int
     */
    public function getClouds(): int
    {
        return $this->clouds;
    }

    /**
     * Get weather description
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get date format used for `$last_updated`
     * @return string
     */
    public function getDateFormat(): string
    {
        return $this->date_format;
    }

    /**
     * Get human perception of current temperature
     * @return float
     */
    public function getFeelsLike(): float
    {
        return $this->feels_like;
    }

    /**
     * Get humidity in %
     * @return int
     */
    public function getHumidity(): int
    {
        return $this->humidity;
    }

    /**
     * Get weather icon ID
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get last update time as a formatted string
     * @return string
     */
    public function getLastUpdateTime(): string
    {
        return $this->last_updated->format($this->date_format);
    }

    /**
     * Get precipitation of rain in mm/h
     * @return float|null
     */
    public function getRain(): ?float
    {
        return $this->rain;
    }

    /**
     * Get precipitation of snow in mm/h
     * @return float|null
     */
    public function getSnow(): ?float
    {
        return $this->snow;
    }

    /**
     * Get current temperature
     * @return float
     */
    public function getTemperature(): float
    {
        return $this->temperature;
    }

    /**
     * Get maximum temperature observed
     * @return float
     */
    public function getTemperatureMax(): float
    {
        return $this->temperature_max;
    }

    /**
     * Get minimum temperature observed
     * @return float
     */
    public function getTemperatureMin(): float
    {
        return $this->temperature_min;
    }

    /**
     * Get atmospheric pressure in hPa
     * @return int
     */
    public function getPressure(): int
    {
        return $this->pressure;
    }

    /**
     * Get visibility in meters
     * @return int
     */
    public function getVisibility(): int
    {
        return $this->visibility;
    }

    /**
     * Get weather name
     * @return string
     */
    public function getWeather(): string
    {
        return $this->weather;
    }

    /**
     * Get wind data
     * @return Wind
     */
    public function getWind(): Wind
    {
        return $this->wind;
    }

    /**
     * Get units for parameter formatting
     * @return string
     */
    public function getUnits(): string
    {
        return $this->units;
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
     * Weather data is updated every 10 minutes, check if an update is available
     * @return bool
     */
    public function isUpdateAvailable(): bool
    {
        $diff = abs((new \DateTime())->getTimestamp() - $this->last_updated->getTimestamp());
        return $diff >= 600; // 600 seconds = 10 minutes
    }
}
