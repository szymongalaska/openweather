<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

use Bejblade\OpenWeather\OpenWeatherDate;
use Bejblade\OpenWeather\Config;

/**
 * Weather model
 */
class Weather
{
    /** Weather name
     *  @var string|null
     */
    private ?string $weather;

    /** Weather description
     * @var string|null
     */
    private ?string $description;

    /** Weather icon id
     * @var string|null
     */
    private ?string $icon;

    /** Sunrise time
     * @var OpenWeatherDate|null
     */
    private ?OpenWeatherDate $sunrise;

    /** Sunset time
     * @var OpenWeatherDate|null
     */
    private ?OpenWeatherDate $sunset;

    /** Temperature data
     * @var Temperature
     */
    private Temperature $temperature;

    /** Atmospheric pressure on the sea level in hPa
     * @var int|null
     */
    private ?int $pressure;

    /** Humidity in %
     * @var int|null
     */
    private ?int $humidity;

    /** Visibility in meters, maximum is 10km
     * @var int|null
     */
    private ?int $visibility;

    /** Wind data of current weather
     * @var Wind|null
     */
    private ?Wind $wind;

    /** Cloudiness in %
     * @var int|null
     */
    private ?int $clouds;

    /** Precipitation of rain in mm/h
     * @var float|null
     */
    private ?float $rain;

    /** Precipitation of snow in mm/h
     * @var float|null
     */
    private ?float $snow;

    /** Probability of precipitation (only in forecast)
     * @var float|null
     */
    private ?float $probabilityOfPrecipitation = null;

    /** Represents the timestamp of the last weather update. For forecasts, this property contains the date of the forecasted weather.
     * @var OpenWeatherDate
     */
    private OpenWeatherDate $weatherTimestamp;

    /** Units in which some of parameters are formatted
     * @var string
     */
    private string $units;

    public function __construct(array $data)
    {
        $this->weather = $data['weather'][0]['main'] ?? null;
        $this->description = $data['weather'][0]['description'] ?? null;
        $this->icon = $data['weather'][0]['icon'] ?? null;
        $this->sunrise = isset($data['sunrise']) ? new OpenWeatherDate("@{$data['sunrise']}") : null;
        $this->sunset = isset($data['sunset']) ? new OpenWeatherDate("@{$data['sunset']}") : null;
        $this->temperature = new Temperature($data['temperature']);
        $this->pressure = $data['main']['pressure'] ?? null;
        $this->humidity = $data['main']['humidity'] ?? null;
        $this->visibility = $data['visibility'] ?? null;
        $this->wind = isset($data['wind']) ? new Wind($data['wind']) : null;
        $this->clouds = $data['clouds']['all'] ?? null;
        $this->rain = isset($data['rain']) ? $this->determinePrecipitation($data['rain']) : null;
        $this->snow = isset($data['snow']) ? $this->determinePrecipitation($data['snow']) : null;
        $this->probabilityOfPrecipitation = $data['pop'] ?? null;
        $this->weatherTimestamp = new OpenWeatherDate("@{$data['dt']}");
        $this->units = Config::configuration()->get('units');
    }

    /**
     * Get precipitation value to set
     * @param array $precipitation
     * @return float|null
     */
    private function determinePrecipitation(array $precipitation): float|null
    {
        if (empty($precipitation['1h']) && empty($precipitation['3h'])) {
            return null;
        }

        return array_key_first($precipitation) == '1h' ? $precipitation['1h'] : $precipitation['3h'] / 3;
    }

    /**
     * Get weather name
     * @return string|null
     */
    public function getWeather(): ?string
    {
        return $this->weather;
    }

    /**
     * Get weather description
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get weather icon id
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Get temperature data
     * @return Temperature
     */
    public function temperature(): Temperature
    {
        return $this->temperature;
    }

    /**
     * Get atmospheric pressure
     * @return int|null
     */
    public function getPressure(): ?int
    {
        return $this->pressure;
    }

    /**
     * Get humidity
     * @return int|null
     */
    public function getHumidity(): ?int
    {
        return $this->humidity;
    }

    /**
     * Get visibility
     * @return int|null
     */
    public function getVisibility(): ?int
    {
        return $this->visibility;
    }

    /**
     * Get wind data
     * @return Wind|null
     */
    public function wind(): ?Wind
    {
        return $this->wind;
    }

    /**
     * Get cloudiness
     * @return int|null
     */
    public function getClouds(): ?int
    {
        return $this->clouds;
    }

    /**
     * Get rain precipitation
     * @return float|null
     */
    public function getRain(): ?float
    {
        return $this->rain;
    }

    /**
     * Get snow precipitation
     * @return float|null
     */
    public function getSnow(): ?float
    {
        return $this->snow;
    }

    /**
     * Get probability of precipitation
     * @return float|null
     */
    public function getProbabilityOfPrecipitation(): ?float
    {
        return $this->probabilityOfPrecipitation;
    }

    /**
     * Get last update time object
     * @return OpenWeatherDate
     */
    public function getTimestamp(): OpenWeatherDate
    {
        return $this->weatherTimestamp;
    }

    /**
     * Get formatted date and time of last data calculation
     * @return string
     */
    public function getDate(): string
    {
        return $this->weatherTimestamp->getFormatted();
    }

    /**
     * Get units
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
     * Weather data is updated every 10 minutes, check if 10 minutes passed since last fetch.
     * @return bool
     */
    public function isUpdateAvailable(): bool
    {
        $diff = abs((new \DateTime('now', $this->weatherTimestamp->getTimezone()))->getTimestamp() - $this->weatherTimestamp->getTimestamp());

        return $diff >= 600; // 10 minutes
    }

}
