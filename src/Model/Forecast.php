<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

use Bejblade\OpenWeather\Exception\UnsupportedFieldTypeException;

class Forecast implements \Countable
{
    /**
     * Collection of weather
     * @var Weather[]
     */
    private array $collection = [];

    /**
     * Array that stores date of first and last element in collection
     * @var array{start:\DateTimeInterface, end:\DateTimeInterface}
     */
    private array $dateRange;

    public function __construct(array $list)
    {
        $this->collection = $this->createCollection($list);
        $this->dateRange = [
            'start' => reset($this->collection)->getTimestamp(),
            'end' => end($this->collection)->getTimestamp(),
        ];
    }

    /**
     * Get collection
     * @return array
     */
    public function all(): array
    {
        return $this->collection;
    }

    /**
     * Count elements of collection
     * @return int
     */
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
     * Get the number of days between `dateRange` 
     * @return int
     */
    private function numberOfDays(): int
    {
        return abs((int) $this->dateRange['start']->format('d') - (int) $this->dateRange['end']->format('d'));
    }

    /**
     * Get forecast for the rest of the day
     * @return Forecast|bool
     */
    public function getForecastForToday(): Forecast|bool
    {
        return $this->getWeatherForDay();
    }

    /**
     * Get forecast for tomorrow
     * @return Forecast|bool
     */
    public function getForecastForTomorrow(): Forecast|bool
    {
        return $this->getWeatherForDay(1);
    }

    /**
     * Get forecast for a specific day. Returns false if there is no forcast for that day or current forecast is the same.
     * @param int $day The number of days that have passed since the start in `dateRange`. Default 0 (start date)
     * @return Forecast|bool
     */
    public function getWeatherForNext(int $day): Forecast|bool
    {
        return $this->getWeatherForDay($day);
    }

    /**
     * Get forecast for a specific day. Returns false if there is no forcast for that day or current forecast is the same.
     * @param int $day The number of days that have passed since the start in `dateRange`. Default 0 (start date)
     * @throws \OutOfRangeException Thrown when argument `day` is less than 0 or more than number of days between first and last elements of collection
     * @return Forecast|bool
     */
    private function getWeatherForDay(int $day = 0): Forecast|bool
    {
        if ($day < 0 || $day > $this->numberOfDays())
            throw new \OutOfRangeException("Data for requested day is not available");

        $date = new \DateTime("+{$day} day");

        $forecasts = [];

        foreach ($this->collection as $weather) {
            if ($weather->getTimestamp()->isSameDay($date)) {
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
     * @throws UnsupportedFieldTypeException Thrown when `feelsLike` property is an instance of `Temperature`
     * @return float|null
     */
    private function getAverage(string $property): float|null
    {
        if (!in_array($property, ['temperature', 'maxTemperature', 'minTemperature', 'feelsLike', 'humidity', 'visibility', 'pressure', 'clouds'])) {
            throw new \InvalidArgumentException('Average value for that property is not available or property does not exist.');
        }

        $property = match ($property) {
            'temperature' => '',
            'maxTemperature' => 'maximum',
            'minTemperature' => 'minimum',
            default => $property
        };

        $method = 'get' . ucfirst($property);
        foreach ($this->collection as $weather) {
            if (in_array($property, ['', 'current', 'maximum', 'minimum', 'feelsLike'])) {
                $value = $weather->temperature()->$method();

                if ($value instanceof Temperature) {
                    throw new UnsupportedFieldTypeException("Cannot calculate average temperature: feelsLike is an instance of Temperature");
                }

                $values[] = $value;
            } else {
                $values[] = $weather->$method();
            }
        }

        $values = array_filter($values);

        if (empty($values)) {
            return null;
        }

        $averageValue = array_sum($values) / count($values);
        return round($averageValue, 2);
    }

    /**
     * Get average temperature of collection
     * @return float|null
     */
    public function averageTemperature(): float|null
    {
        return $this->getAverage('temperature');
    }

    /**
     * Get average maximum temperature of collection
     * @return float|null
     */
    public function averageMaxTemperature(): float|null
    {
        return $this->getAverage('maxTemperature');
    }

    /**
     * Get average minimum temperature of collection
     * @return float|null
     */
    public function averageMinTemperature(): float|null
    {
        return $this->getAverage('minTemperature');
    }

    /**
     * Get average temperature of collection considering human perception
     * @return float|null
     */
    public function averageFeelsLike(): float|null
    {
        return $this->getAverage('feelsLike');
    }

    /**
     * Get average humidity of collection
     * @return float|null
     */
    public function averageHumidity(): float|null
    {
        return $this->getAverage('humidity');
    }

    /**
     * Get average visibility of collection
     * @return float|null
     */
    public function averageVisibility(): float|null
    {
        return $this->getAverage('visibility');
    }

    /**
     * Get average pressure of collection
     * @return float|null
     */
    public function averagePressure(): float|null
    {
        return $this->getAverage('pressure');
    }

    /**
     * Get average cloudiness of collection
     * @return float|null
     */
    public function averageClouds(): float|null
    {
        return $this->getAverage('clouds');
    }

    /**
     * Checks if it will rain during the forecast period by evaluating precipitation probabilities assuming that when probability is less than 50% it will not rain. Returns false when no rain precipitaion data is available
     * @return bool
     */
    public function willItRain(): bool
    {
        return $this->willIt('rain');
    }

    /**
     * Checks if it will snow during the forecast period by evaluating precipitation probabilities assuming that when probability is less than 50% it will not rain. Returns false when no snow precipitaion data is available
     * @return bool
     */
    public function willItSnow(): bool
    {
        return $this->willIt('snow');
    }

    /**
     * Checks if it will snow or rain during the forecast period by evaluating precipitation probabilities assuming that when probability is less than 50% it will not snow. Returns false when no precipitaion data is available
     * @param string $precipitation Precipitation type:
     * - snow
     * - rain
     *
     * @throws \InvalidArgumentException Thrown when `precipitation` is neither snow nor rain
     * @return bool
     */
    private function willIt(string $precipitation): bool
    {
        if (!in_array($precipitation, ['snow', 'rain'])) {
            throw new \InvalidArgumentException('Invalid argument provided.');
        }

        $method = 'get' . ucfirst($precipitation);

        foreach ($this->collection as $weather) {
            $values[] = $weather->$method() ? $weather->getProbabilityOfPrecipitation() >= 0.5 : false;
        }

        $trueValues = count(array_filter($values));

        return $trueValues >= count($values) - $trueValues;
    }
}
