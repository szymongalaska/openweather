<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

class Precipitation
{
    private float $precipitation;

    private int $time;

    private ?float $propability = null;

    public function __construct(array $data, ?float $propability = null)
    {
        $this->precipitation = $data[array_key_first($data)];
        $this->time = array_key_first($data) == '1h' ? 1 : 3;
        $this->propability = $propability;
    }

    public function getPrecipitation(): float
    {
        return $this->precipitation;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getPropability(): float|null
    {
        return $this->getPropability();
    }
}