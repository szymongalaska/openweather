<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

/**
 * Wind model
 */
class Wind
{
    /**
     * Wind speed in meter/sec or miles/hour
     * @var int
     */
    public float $speed;

    /**
     * Wind direction in meteorogical degrees
     * @var int
     */
    public float $direction;

    /**
     * Wind gust in meter/sec or miles/hour
     * @var int
     */
    public ?float $gust;

    public function __construct(array $data)
    {
        $this->speed = $data['speed'];
        $this->direction = $data['deg'];
        $this->gust = $data['gust'] ?? null;
    }
}
