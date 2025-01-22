<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

use Bejblade\OpenWeather\OpenWeatherDate;
use Bejblade\OpenWeather\Config;

class Weather
{
    /** @var string|null Weather name */
    private ?string $weather;

    /** @var string|null Weather description */
    private ?string $description;

    /** @var string|null Weather icon id */
    private ?string $icon;

    /** @var OpenWeatherDate|null Sunrise time */
    private ?OpenWeatherDate $sunrise;

    /** @var OpenWeatherDate|null Sunset time */
    private ?OpenWeatherDate $sunset;

    /** @var Temperature Current temperature data */
    private Temperature $temperature;

    /** @var int|null Atmospheric pressure on the sea level in hPa */
    private ?int $pressure;

    /** @var int|null Humidity in % */
    private ?int $humidity;

    /** @var int|null Visibility in meters, maximum is 10km */
    private ?int $visibility;

    /** @var Wind|null Wind data of current weather */
    private ?Wind $wind;

    /** @var int|null Cloudiness in % */
    private ?int $clouds;

    /** @var float|null Precipitation of rain in mm/h */
    private ?float $rain;

    /** @var float|null Precipitation of snow in mm/h */
    private ?float $snow;

    /** @var float|null Probability of precipitation (only in forecast) */
    private ?float $probabilityOfPrecipitation = null;

    /** @var OpenWeatherDate and time of last data calculation */
    private OpenWeatherDate $lastUpdated;

    /** @var string Units in which some of parameters are formatted */
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
        $this->lastUpdated = new OpenWeatherDate("@{$data['dt']}");
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
     * @return string
     */
    public function getWeather(): string
    {
        return $this->weather;
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
     * Get weather icon id
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    public function temperature(): Temperature
    {
        return $this->temperature;
    }

    /**
     * Get atmospheric pressure
     * @return int
     */
    public function getPressure(): int
    {
        return $this->pressure;
    }

    /**
     * Get humidity
     * @return int
     */
    public function getHumidity(): int
    {
        return $this->humidity;
    }

    /**
     * Get visibility
     * @return int
     */
    public function getVisibility(): int
    {
        return $this->visibility;
    }

    /**
     * Get wind data
     * @return Wind
     */
    public function wind(): Wind
    {
        return $this->wind;
    }

    /**
     * Get cloudiness
     * @return int
     */
    public function getClouds(): int
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
    public function getLastUpdated(): OpenWeatherDate
    {
        return $this->lastUpdated;
    }

    /**
     * Get formatted date and time of last data calculation
     * @return string
     */
    public function getLastUpdateTime(): string
    {
        return $this->lastUpdated->getFormatted();
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
        $diff = abs((new \DateTime('now', $this->lastUpdated->getTimezone()))->getTimestamp() - $this->lastUpdated->getTimestamp());

        return $diff >= 600; // 10 minutes
    }

}
