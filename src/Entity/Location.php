<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Entity;

use Bejblade\OpenWeather\Entity\Forecast;

/**
 * Location entity
 */
class Location
{
    /**
     * ISO 3166 Country code
     * @var string
     */
    private string $country;

    /**
     * Latitude of location
     * @var float
     */
    private float $latitude;

    /**
     * Array of location names in different languages
     * @var array|null
     */
    private ?array $localNames;

    /**
     * Longitude of location
     * @var float
     */
    private float $longitude;

    /**
     * Location name
     * @var string
     */
    private string $name;

    /**
     * State of location
     * @var string|null
     */
    private ?string $state;

    /**
     * Weather data
     * @var Weather|null
     */
    private ?Weather $weather = null;

    /**
     * Forecast data
     * @var Forecast|null
     */
    private ?Forecast $forecast = null;

    /**
     * AirPollution data
     * @var AirPollution|null
     */
    private ?AirPollution $airPollution = null;

    /**
     * @param array $data Array containing location information
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->localNames = $data['local_names'] ?? null;
        $this->latitude = $data['lat'];
        $this->longitude = $data['lon'];
        $this->country = $data['country'];
        $this->state = $data['state'] ?? null;
    }

    /**
     * Get ISO 3166 country code
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Get location latitude
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * Get location name in specified language
     *
     * @param string $language Language code
     * @return string|null
     */
    public function getLocalName(string $language): string|null
    {
        return $this->localNames[$language] ?? null;
    }

    /**
     * Get location longitude
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * Get location name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get state of location
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Get coordinates of location as a string
     * @return string
     */
    public function getCoordinates(): string
    {
        return $this->latitude . ', ' . $this->longitude;
    }

    /**
     * Get forecast
     * @return Forecast|null
     */
    public function forecast(): ?Forecast
    {
        return $this->forecast;
    }

    /**
     * Get weather object
     * @return Weather|null
     */
    public function weather(): ?Weather
    {
        return $this->weather;
    }

    /**
     * Get air pollution object
     * @return AirPollution|null
     */
    public function airPollution(): ?AirPollution
    {
        return $this->airPollution;
    }

    /**
     * Check if forecast is set
     * @return bool
     */
    public function hasForecast(): bool
    {
        return $this->forecast !== null;
    }

    /**
     * Check if weather is set
     * @return bool
     */
    public function hasWeather(): bool
    {
        return $this->weather !== null;
    }

    /**
     * Check if air pollution is set
     * @return bool
     */
    public function hasAirPollution(): bool
    {
        return $this->airPollution !== null;
    }

    /**
     * Set forecast
     *
     * @param Forecast $forecast
     * @return void
     */
    public function setForecast(Forecast $forecast): void
    {
        $this->forecast = $forecast;
    }

    /**
     * Set weather object
     *
     * @param Weather $weather
     * @return void
     */
    public function setWeather(Weather $weather): void
    {
        $this->weather = $weather;
    }

    /**
     * Set air pollution object
     *
     * @param AirPollution $airPollution
     * @return void
     */
    public function setAirPollution(AirPollution $airPollution): void
    {
        $this->airPollution = $airPollution;
    }
}
