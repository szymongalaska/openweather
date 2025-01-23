<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

use Bejblade\OpenWeather\Config;

/**
 * Temperature model
 */
class Temperature
{
    /** Current or day average temperature
     * @var float|null
     */
    private ?float $temperature;

    /** Maximum temperature
     * @var float|null
     */
    private ?float $maximum;

    /** Minimum temperature
     * @var float|null
     */
    private ?float $minimum;

    /** Human perception of current temperature
     * @var Temperature|float|null
     */
    private Temperature|float|null $feelsLike;

    /** Temperature at 06:00
     * @var float|null
     */
    private ?float $morning;

    /** Temperature at 12:00
     * @var float|null
     */
    private ?float $afternoon;

    /** Temperature at 18:00
     * @var float|null
     */
    private ?float $evening;

    /** Temperature at 00:00
     * @var float|null
     */
    private ?float $night;

    /**
     * Units of temperature data
     * @var string
     */
    private string $units;

    public function __construct(array $data)
    {
        $this->temperature = $data['temp'] ?? null;

        if (isset($data['feels_like']) && is_array($data['feels_like'])) {
            $this->feelsLike = new self($data['feels_like']);
        } else {
            $this->feelsLike = $data['feels_like'] ?? null;
        }

        $this->maximum = $data['max'] ?? null;
        $this->minimum = $data['min'] ?? null;
        $this->morning = $data['morning'] ?? null;
        $this->afternoon = $data['afternoon'] ?? null;
        $this->evening = $data['evening'] ?? null;
        $this->night = $data['night'] ?? null;
        $this->setUnits();
    }

    /**
     * Set value to `units` property
     * @return void
     */
    private function setUnits(): void
    {
        $this->units = match (Config::configuration()->get('units')) {
            'metric' => 'Celsius',
            'imperial' => 'Fahrenheit',
            default => 'Kelvin'
        };
    }

    /**
     * Get current or day average temperature
     * @return float|null
     */
    public function get(): float|null
    {
        return $this->temperature;
    }

    /**
     * Get feels like temperature
     * @return Temperature|float|null
     */
    public function getFeelsLike(): Temperature|float|null
    {
        return $this->feelsLike;
    }

    /**
     * Get minimum temperature
     * @return float|null
     */
    public function getMinimum(): float|null
    {
        return $this->minimum;
    }

    /**
     * Get maximum temperature
     * @return float|null
     */
    public function getMaximum(): float|null
    {
        return $this->maximum;
    }

    /**
     * Get temperature at 06:00
     * @return float|null
     */
    public function getMorning(): float|null
    {
        return $this->morning;
    }

    /**
     * Get temperature at 12:00
     * @return float|null
     */
    public function getAfternoon(): float|null
    {
        return $this->afternoon;
    }

    /**
     * Get temperature at 18:00
     * @return float|null
     */
    public function getEvening(): float|null
    {
        return $this->evening;
    }

    /**
     * Get temperature at 00:00
     * @return float|null
     */
    public function getNight(): float|null
    {
        return $this->night;
    }
}
