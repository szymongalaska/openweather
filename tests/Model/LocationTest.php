<?php

namespace Tests;

use Bejblade\OpenWeather\Model\Location;

class LocationTest extends BaseTestCase
{
    protected $location;
    protected function setUp(): void
    {
        parent::setUp();
        $this->location = new Location($this->fixture('location'));
    }

    protected function tearDown(): void
    {
        unset($this->location);
        parent::tearDown();
    }

    public function test_get_coordinates_returns_coords()
    {
        $this->assertSame('51.5073219, -0.1276474', $this->location->getCoordinates());
    }

    public function test_get_local_name_returns_name_in_give_language(): void
    {
        $this->assertSame('Londyn', $this->location->getLocalName('pl'));
    }

    public function test_has_weather_returns_values(): void
    {
        $this->assertFalse($this->location->hasWeather());

        $weather = new \Bejblade\OpenWeather\Model\Weather($this->fixture('weather'), $this->fixture('weather')['timezone']);
        $this->location->setWeather($weather);

        $this->assertTrue($this->location->hasWeather());
    }

    public function test_has_forecast_returns_values(): void
    {
        $this->assertFalse($this->location->hasForecast());

        $forecast = new \Bejblade\OpenWeather\Model\Forecast($this->fixture('forecast')['list'], $this->fixture('forecast')['city']['timezone']);
        $this->location->setForecast($forecast);

        $this->assertTrue($this->location->hasForecast());
    }

    public function test_forecast_returns_forecast(): void
    {
        $this->assertNull($this->location->forecast());

        $forecast = new \Bejblade\OpenWeather\Model\Forecast($this->fixture('forecast')['list'], $this->fixture('forecast')['city']['timezone']);
        $this->location->setForecast($forecast);

        $this->assertInstanceOf(\Bejblade\OpenWeather\Model\Forecast::class, $this->location->forecast());
    }

    public function test_weather_returns_weather(): void
    {
        $this->assertNull($this->location->weather());

        $weather = new \Bejblade\OpenWeather\Model\Weather($this->fixture('weather'), $this->fixture('weather')['timezone']);
        $this->location->setWeather($weather);

        $this->assertInstanceOf(\Bejblade\OpenWeather\Model\Weather::class, $this->location->weather());
    }

    public function test_has_air_pollution_returns_values(): void
    {
        $this->assertFalse($this->location->hasAirPollution());

        $airPollution = new \Bejblade\OpenWeather\Model\AirPollution($this->fixture('air_pollution'));
        $this->location->setAirPollution($airPollution);

        $this->assertTrue($this->location->hasAirPollution());
    }

    public function test_air_pollution_returns_air_pollution(): void
    {
        $this->assertNull($this->location->airPollution());

        $airPollution = new \Bejblade\OpenWeather\Model\AirPollution($this->fixture('air_pollution'));
        $this->location->setAirPollution($airPollution);

        $this->assertInstanceOf(\Bejblade\OpenWeather\Model\AirPollution::class, $this->location->airPollution());
    }

}
