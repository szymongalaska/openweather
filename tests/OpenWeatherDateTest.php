<?php

namespace Tests;

use Bejblade\OpenWeather\OpenWeatherDate;

class OpenWeatherDateTest extends BaseTestCase
{
    public function test_is_same_day_works(): void
    {
        $timestamp = $this->fixture('weather')['dt'];
        $openWeatherDate = new OpenWeatherDate("@{$timestamp}");
        $date = new \DateTime("@{$timestamp}");
        $actual = $openWeatherDate->isSameDay($date);

        $this->assertTrue($actual);

        $date->modify('+1 day');
        $actual = $openWeatherDate->isSameDay($date);
        $this->assertFalse($actual);
    }
}
