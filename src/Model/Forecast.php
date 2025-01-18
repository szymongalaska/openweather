<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

class Forecast
{
    /**
     * Weather list
     * @var Weather[]
     */
    private array $forecasts;

    /**
     * @param Weather[] $forecasts
     */
    public function __construct($forecasts)
    {
        $this->forecasts = $forecasts;
    }

    public function getForecasts(): array
    {
        return $this->forecasts;
    }
}
