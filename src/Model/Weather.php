<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

use Bejblade\OpenWeather\Config;

class Weather
{
    /** @var string Weather name */
    private string $weather;

    /** @var string Weather description */
    private string $description;

    /** @var string Weather icon id */
    private string $icon;

    /** @var float Current temperature */
    private float $temperature;

    /** @var float Human perception of current temperature */
    private float $feelsLike;

    /** @var float Minimum temperature observed within large megalopolises and urban areas */
    private float $temperatureMin;

    /** @var float Maximum temperature observed within large megalopolises and urban areas */
    private float $temperatureMax;

    /** @var int Atmospheric pressure on the sea level in hPa */
    private int $pressure;

    /** @var int Humidity in % */
    private int $humidity;

    /** @var int Visibility in meters, maximum is 10km */
    private int $visibility;

    /** @var Wind Wind data of current weather */
    private Wind $wind;

    /** @var int Cloudiness in % */
    private int $clouds;

    /** @var float|null Precipitation of rain in mm/h */
    private ?float $rain;

    /** @var float|null Precipitation of snow in mm/h */
    private ?float $snow;

    /** @var float|null Propability of precipitation (only in forecast) */
    private ?float $propabilityOfPrecipitation = null;

    /** @var \DateTime Date and time of last data calculation */
    private \DateTime $lastUpdated;

    /** @var string Date format applied to `$lastUpdated` */
    private string $dateFormat;

    /** @var string Units in which some of parameters are formatted */
    private string $units;

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
        $this->rain = isset($data['rain']) ? $this->setRain($data['rain']) : null;
        $this->snow = isset($data['snow']) ? $this->setSnow($data['snow']) : null;
        $this->propabilityOfPrecipitation = $data['pop'] ?? null;
        $this->lastUpdated = new \DateTime('@' . $data['dt']);
        $this->lastUpdated->setTimezone(new \DateTimeZone(Config::configuration()->get('timezone')));
        $this->dateFormat = $this->getDateFormat();
        $this->units = Config::configuration()->get('units');
    }

    private function setRain(array $rain): float
    {
        return array_key_first($rain) == '1h' ? $rain['1h'] : $rain['3h'] / 3;
    }

    private function setSnow(array $snow): float
    {
        return array_key_first($snow) == '1h' ? $snow['1h'] : $snow['3h'] / 3;
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

    /**
     * Get current temperature
     * @return float
     */
    public function getTemperature(): float
    {
        return $this->temperature;
    }

    /**
     * Get feels like temperature
     * @return float
     */
    public function getFeelsLike(): float
    {
        return $this->feelsLike;
    }

    /**
     * Get minimum temperature
     * @return float
     */
    public function getTemperatureMin(): float
    {
        return $this->temperatureMin;
    }

    /**
     * Get maximum temperature
     * @return float
     */
    public function getTemperatureMax(): float
    {
        return $this->temperatureMax;
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
    public function getWind(): Wind
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
     * Get last update time
     * @return \DateTime
     */
    public function getLastUpdated(): \DateTime
    {
        return $this->lastUpdated;
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

    /**
     * Set dateFormat according to configuration
     * @return string
     */
    private function getDateFormat(): string
    {
        return $this->dateFormat = preg_replace(
            '/d|D|j|l|N|S|w|z/',
            Config::configuration()->get('day_format'),
            Config::configuration()->get('date_format') . ' ' . Config::configuration()->get('time_format')
        );
    }

    /**
     * Helper method that checks if weather last update date is same as given date
     * @param \DateTimeInterface $date Date to compare
     * @return bool
     */
    public function isSameDay(\DateTimeInterface $date): bool
    {
        return $date->format('Y-m-d') == $this->lastUpdated->format('Y-m-d');
    }
}
