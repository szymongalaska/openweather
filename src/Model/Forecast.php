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

    /**
     * Get all forecasts
     * @return array
     */
    public function all(): array
    {
        return $this->forecasts;
    }

    /**
     * Get the forecast for the rest of the day
     * @return array
     */
    public function getForecastForToday(): Forecast|bool
    {
        return $this->getForecastForDay();
    }

    /**
     * Get the forecast for tomorrow
     * @return array
     */
    public function getForecastForTomorrow(): Forecast|bool
    {
        return $this->getForecastForDay(1);
    }

    /**
     * Get forecast for a specific day. Returns false if there is no forcast for that day or current forecast is the same. The furthest day you can check is 5
     * @param int $day
     * @return array
     */
    public function getForecastForNext(int $day): Forecast|bool
    {
        return $this->getForecastForDay($day);
    }

    /**
     * Get forecast for a specific day. Returns false if there is no forcast for that day or current forecast is the same. The furthest day you can check is 5
     * @param int $day
     * @throws \InvalidArgumentException
     * @return Forecast|bool
     */
    private function getForecastForDay(int $day = 0): Forecast|bool
    {
        if ($day == 0) {
            $date = new \DateTime();
        } elseif ($day <= 5) {
            $date = new \DateTime("+{$day} day");
        } else {
            throw new \InvalidArgumentException('');
        }

        $forecasts = [];

        foreach ($this->forecasts as $weather) {
            if ($weather->isSameDay($date)) {
                $forecasts[] = $weather;
            }
        }

        if (!empty($forecasts) && $forecasts !== $this->forecasts) {
            return new Forecast($forecasts);
        } else {
            return false;
        }
    }

    /**
     * Calculate average value of given property
     * @param string $property Property to calculate
     * - temperature
     * - maxTemperature
     * - minTemperature
     * - feelsLike
     * - humidity
     * - visibility
     * - pressure
     * - clouds
     * 
     * @throws \InvalidArgumentException Thrown when property value cannot be calculated or property does not exist
     * @return int
     */
    private function getAverage(string $property)
    {
        if (!in_array($property, ['temperature', 'maxTemperature', 'minTemperature', 'feelsLike', 'humidity', 'visibility', 'pressure', 'clouds']))
            throw new \InvalidArgumentException('Average value for that property is not available or property does not exist.');

        $method = 'get' . ucfirst($property);
        foreach ($this->forecasts as $weather) {
            $values[] = $weather->$method();
        }

        $averageValue = array_sum($values) / count($values);
        return (int) $averageValue;
    }

    /**
     * Get average temperature for current forecast
     * @return int
     */
    public function averageTemperature(): int
    {
        return $this->getAverage('temperature');
    }

    /**
     * Get average max temperature for current forecast
     * @return int
     */
    public function averageMaxTemperature(): int
    {
        return $this->getAverage('maxTemperature');
    }


    /**
     * Get average min temperature for current forecast
     * @return int
     */
    public function averageMinTemperature(): int
    {
        return $this->getAverage('minTemperature');
    }


    /**
     * Get average temperature for current forecast considering human perception
     * @return int
     */
    public function averageFeelsLike(): int
    {
        return $this->getAverage('feelsLike');
    }

    /**
     * Get average humidity for current forecast
     * @return int
     */
    public function averageHumidity(): int
    {
        return $this->getAverage('humidity');
    }

    /**
     * Get average visibility for current forecast
     * @return int
     */
    public function averageVisibility(): int
    {
        return $this->getAverage('visibility');
    }

    /**
     * Get average pressure for current forecast
     * @return int
     */
    public function averagePressure(): int
    {
        return $this->getAverage('pressure');
    }

    /**
     * Get average cloudiness for current forecast
     * @return int
     */
    public function averageClouds(): int
    {
        return $this->getAverage('clouds');
    }

    /**
     * Checks if it will rain during the forecast period by evaluating precipitation probabilities assuming that when probability is less than 50% it will not rain
     * @return bool
     */
    public function willItRain(): bool
    {
        foreach ($this->forecasts as $weather) {
            $rain[] = $weather->getRain() ? $weather->getProbabilityOfPrecipitation() >= 0.5 : false;
        }

        $trueValues = count(array_filter($rain));

        return $trueValues >= count($rain) - $trueValues;
    }

    /**
     * Checks if it will snow during the forecast period by evaluating precipitation probabilities assuming that when probability is less than 50% it will not snow
     * @return bool
     */
    public function willItSnow(): bool
    {
        foreach ($this->forecasts as $weather) {
            $rain[] = $weather->getSnow() ? $weather->getProbabilityOfPrecipitation() >= 0.5 : false;
        }

        $trueValues = count(array_filter($rain));

        return $trueValues >= count($rain) - $trueValues;
    }
}
