<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

use Bejblade\OpenWeather\Config;

class Temperature
{
    /** @var float|null Current temperature */
    private ?float $current;

    /** @var float|null Maximum temperature */
    private ?float $maximum;

    /** @var float|null Minimum temperature */
    private ?float $minimum;

    /** @var float|null Human perception of current temperature */
    private ?float $feelsLike;

    /** @var float|null Temperature at 06:00 */
    private ?float $morning;

    /** @var float|null Temperature at 12:00 */
    private ?float $afternoon;

    /** @var float|null Temperature at 18:00 */
    private ?float $evening;

    /** @var float|null Temperature at 00:00 */
    private ?float $night;

    private string $units;

    public function __construct(array $data)
    {
        $this->current = $data['temp'] ?? null;
        $this->feelsLike = $data['feels_like'] ?? null;
        $this->maximum = $data['max'] ?? null;
        $this->minimum = $data['min'] ?? null;
        $this->morning = $data['morning'] ?? null;
        $this->afternoon = $data['afternoon'] ?? null;
        $this->evening = $data['evening'] ?? null;
        $this->night = $data['night'] ?? null;
        $this->setUnits();
    }

    private function setUnits(): void
    {
        $this->units = match (Config::configuration()->get('units')) {
            'metric' => 'Celsius',
            'imperial' => 'Fahrenheit',
            default => 'Kelvin'
        };
    }

    /**
     * Get current temperature
     * @return float|null
     */
    public function getCurrent(): float|null
    {
        return $this->current;
    }

    /**
     * Get feels like temperature
     * @return float|null
     */
    public function getFeelsLike(): float|null
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
