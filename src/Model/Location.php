<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

class Location
{
    /**
     * Location name
     * @var string
     */
    public string $name;

    /**
     * Array of location names in different languages
     * @var array
     */
    public ?array $localNames;

    /**
     * Latitude of location
     * @var float
     */
    public float $latitude;

    /**
     * Longitute of location
     * @var float
     */
    public float $longtitude;

    /**
     * ISO 3166 Country code
     * @var string
     */
    public string $country;

    /**
     * State of location
     * @var string|null
     */
    public ?string $state;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->localNames = $data['local_names'] ?? null;
        $this->latitude = $data['lat'];
        $this->longtitude = $data['lon'];
        $this->country = $data['country'];
        $this->state = $data['state'] ?? null;
    }

    /**
     * Get location name in specified language
     *
     * @param string $language
     * @return string|null
     */
    public function getLocalName(string $language): string|null
    {
        return $this->localNames[$language] ?? null;
    }

    /**
     * Get coordinates of location
     * @return string
     */
    public function getCoordinates(): string
    {
        return $this->latitude . ', ' . $this->longtitude;
    }
}
