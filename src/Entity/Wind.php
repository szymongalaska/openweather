<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Entity;

/**
 * Wind entity
 */
class Wind
{
    /**
     * Wind speed in meter/sec or miles/hour
     * @var float
     */
    private float $speed;

    /**
     * Wind direction in meteorogical degrees
     * @var float
     */
    private float $direction;

    /**
     * Wind gust in meter/sec or miles/hour
     * @var float|null
     */
    public ?float $gust;

    public function __construct(array $data)
    {
        $this->speed = $data['speed'];
        $this->direction = $data['deg'];
        $this->gust = $data['gust'] ?? null;
    }
    /**
     * Get the speed of the wind.
     *
     * @return float The speed of the wind.
     */
    public function getSpeed(): float
    {
        return $this->speed;
    }

    /**
     * Get the direction of the wind.
     *
     * @return float The direction of the wind.
     */
    public function getDirection(): float
    {
        return $this->direction;
    }

    /**
     * Get the gust speed of the wind.
     *
     * @return float|null The gust speed of the wind, or null if not available.
     */
    public function getGust(): ?float
    {
        return $this->gust;
    }
}
