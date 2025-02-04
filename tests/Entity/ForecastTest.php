<?php

namespace Tests;

use Bejblade\OpenWeather\Entity\Forecast;
use Bejblade\OpenWeather\Entity\Weather;

class ForecastTest extends BaseTestCase
{
    protected $forecast;

    protected $weatherList;

    protected function setUp(): void
    {
        parent::setUp();

        // Manually set forecast time
        $weatherList = $this->fixture('forecast')['list'];
        $weatherList[0]['dt'] = time(); // forecast now
        $weatherList[1]['dt'] = time() + 3600; // forecast +1 hour
        $weatherList[2]['dt'] = time() + 24 * 3600; // forecast tomorrow
        $weatherList[3]['dt'] = time() + 48 * 3600;
        $weatherList[4]['dt'] = time() + 72 * 3600;

        $this->weatherList = $weatherList;

        $this->forecast = new Forecast(
            $weatherList,
            $this->fixture('forecast')['city']['timezone']
        );
    }

    protected function tearDown(): void
    {
        unset($this->forecast);
        unset($this->weatherList);
        parent::tearDown();
    }


    public function test_get_forecast_for_tomorrow_returns_forecast_for_tomorrow(): void
    {
        $tomorrowForecasts = new Forecast([$this->weatherList['2']], $this->fixture('forecast')['city']['timezone']);

        $this->assertEquals($tomorrowForecasts, $this->forecast->getForecastForTomorrow());
    }

    public function test_get_forecast_for_today_returns_forecast_for_today(): void
    {
        $todayForecasts = new Forecast([$this->weatherList[0], $this->weatherList[1]], $this->fixture('forecast')['city']['timezone']);

        $this->assertEquals($todayForecasts, $this->forecast->getForecastForToday());
    }

    public function test_get_forecast_for_next_returns_forecast_for_that_day(): void
    {
        $day = 1; // tomorrow

        $tomorrowForecasts = new Forecast([$this->weatherList['2']], $this->fixture('forecast')['city']['timezone']);

        $this->assertEquals($tomorrowForecasts, $this->forecast->getWeatherForNext($day));
    }

    public function test_get_forecast_for_next_return_no_forecast(): void
    {
        $this->forecast = new Forecast([$this->weatherList[0]], $this->fixture('forecast')['city']['timezone']);
        $this->assertFalse($this->forecast->getWeatherForNext(0));
    }

    public function test_get_forecast_for_next_throws_exception(): void
    {
        $day = 99999999;
        $this->expectException(\OutOfRangeException::class);

        $this->forecast->getWeatherForNext($day);
    }

    public function test_get_average_temperature_calculates_correctly(): void
    {
        foreach ($this->fixture('forecast')['list'] as $weather) {
            $temperatures[] = $weather['temperature']['temp'];
        }

        $expected = array_sum($temperatures) / count($temperatures);

        $expected = round($expected, 2);
        $this->assertSame($expected, $this->forecast->averageTemperature());
    }

    public function test_will_it_rain_calcultes_correctly(): void
    {
        $forecastFixture = $this->fixture('forecast')['list'];
        $forecastFixture = array_map(function ($weather) {
            $weather['pop'] = 1;
            $weather['rain']['3h'] = 1;
            return $weather;
        }, $forecastFixture);
        $this->forecast = new Forecast($forecastFixture, $this->fixture('forecast')['city']['timezone']);

        $this->assertTrue($this->forecast->willItRain());

        $forecastFixture = $this->fixture('forecast')['list'];
        $forecastFixture = array_map(function ($weather) {
            $weather['pop'] = 0.33;
            $weather['rain']['3h'] = 1;
            return $weather;
        }, $forecastFixture);
        $this->forecast = new Forecast($forecastFixture, $this->fixture('forecast')['city']['timezone']);

        $this->assertFalse($this->forecast->willItRain());
    }

    public function test_will_it_snow_calculates_correctly(): void
    {
        $forecastFixture = $this->fixture('forecast')['list'];
        $forecastFixture = array_map(function ($weather) {
            $weather['pop'] = 1;
            $weather['snow']['3h'] = 1;
            return $weather;
        }, $forecastFixture);
        $this->forecast = new Forecast($forecastFixture, $this->fixture('forecast')['city']['timezone']);

        $this->assertTrue($this->forecast->willItSnow());

        $forecastFixture = $this->fixture('forecast')['list'];
        $forecastFixture = array_map(function ($weather) {
            $weather['pop'] = 0.33;
            $weather['snow']['3h'] = 1;
            return $weather;
        }, $forecastFixture);
        $this->forecast = new Forecast($forecastFixture, $this->fixture('forecast')['city']['timezone']);

        $this->assertFalse($this->forecast->willItSnow());
    }

    public function test_get_max_temperature_day_returns_correct_day(): void
    {
        $expected = $this->forecast->all()[0]; // Day with maximum temperature
        $this->assertSame($expected, $this->forecast->getMaxTemperatureDay());
    }

    public function test_get_min_temperature_day_returns_correct_day(): void
    {
        $expected = $this->forecast->all()[2]; // Day with minimum temperature
        $this->assertSame($expected, $this->forecast->getMinTemperatureDay());
    }

    public function test_get_cloudiest_day_returns_correct_day(): void

    {
        $expected = $this->forecast->all()[0]; // Day with most clouds
        $this->assertSame($expected, $this->forecast->getCloudiestDay());
    }
}
