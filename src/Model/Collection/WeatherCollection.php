<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model\Collection;

use Bejblade\OpenWeather\OpenWeatherDate;
use Bejblade\OpenWeather\Model\Weather;

class WeatherCollection implements \Countable
{
    /**
     * Collection of weather
     * @var Weather[]
     */
    private array $collection = [];

    public function __construct(array $list)
    {
        $this->collection = $this->createCollection($list);
    }

    /**
     * Get collection
     * @return array
     */
    public function all(): array
    {
        return $this->collection;
    }

    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * Initialize collection as array of Weather[]
     * @param array $list
     * @return Weather[]
     */
    private function createCollection(array $list): array
    {
        return array_map(function ($row) {
            if (!$row instanceof Weather) {
                return new Weather($row);
            }

            return $row;
        }, $list);
    }


    /**
     * Get the forecast for the rest of the day
     * @return WeatherCollection|bool
     */
    public function getForecastForToday(): WeatherCollection|bool
    {
        return $this->getWeatherForDay();
    }

    /**
     * Get the forecast for tomorrow
     * @return WeatherCollection|bool
     */
    public function getForecastForTomorrow(): WeatherCollection|bool
    {
        return $this->getWeatherForDay(1);
    }

    /**
     * Get forecast for a specific day. Returns false if there is no forcast for that day or current forecast is the same. The furthest day you can check is 5
     * @param int $day
     * @return WeatherCollection|bool
     */
    public function getWeatherForNext(int $day): WeatherCollection|bool
    {
        return $this->getWeatherForDay($day);
    }

    /**
     * Get forecast for a specific day. Returns false if there is no forcast for that day or current forecast is the same. The furthest day you can check is 5
     * @param int $day
     * @throws \InvalidArgumentException
     * @return WeatherCollection|bool
     */
    private function getWeatherForDay(int $day = 0): WeatherCollection|bool
    {
        if ($day == 0) {
            $date = new \DateTime();
        } elseif ($day <= 5) {
            $date = new \DateTime("+{$day} day");
        } else {
            throw new \InvalidArgumentException('');
        }

        $forecasts = [];

        foreach ($this->collection as $weather) {
            if ($weather->getLastUpdated()->isSameDay($date)) {
                $forecasts[] = $weather;
            }
        }

        if (!empty($forecasts) && $forecasts !== $this->collection) {
            return new self($forecasts);
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
        if (!in_array($property, ['temperature', 'maxTemperature', 'minTemperature', 'feelsLike', 'humidity', 'visibility', 'pressure', 'clouds'])) {
            throw new \InvalidArgumentException('Average value for that property is not available or property does not exist.');
        }

        $property = match($property) {
            'temperature' => '',
            'maxTemperature' => 'max',
            'minTemperature' => 'min',
            default => $property
        };

        $method = 'get' . ucfirst($property);
        foreach ($this->collection as $weather) {
            if (in_array($property, ['', 'current', 'max', 'min', 'feelsLike'])) {
                $values[] = $weather->temperature()->$method();
            } else {
                $values[] = $weather->$method();
            }
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
        foreach ($this->collection as $weather) {
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
        foreach ($this->collection as $weather) {
            $rain[] = $weather->getSnow() ? $weather->getProbabilityOfPrecipitation() >= 0.5 : false;
        }

        $trueValues = count(array_filter($rain));

        return $trueValues >= count($rain) - $trueValues;
    }
}
