<?php

namespace Tests;

use Bejblade\OpenWeather\Model\Weather;

class WeatherTest extends BaseTestCase
{
    protected $weather;
    protected function setUp(): void
    {
        parent::setUp();
        $this->weather = new Weather(
            $this->fixture('weather')
        );
    }

    protected function tearDown(): void
    {
        unset($this->weather);
        parent::tearDown();
    }

    public function test_get_last_update_time_returns_formatted_date(): void
    {
        $this->assertSame('Wednesday/09/2024 11:59', $this->weather->getDate());
    }

    public function test_is_raining_returns_value(): void
    {
        $this->assertTrue($this->weather->isRaining());
    }

    public function test_is_snowing_returns_value(): void
    {
        $this->assertFalse($this->weather->isSnowing());
    }

    public function test_is_update_available_returns_value(): void
    {
        $this->assertTrue($this->weather->isUpdateAvailable());

        $weatherFixture = $this->fixture('weather');
        $weatherFixture['dt'] = time();

        $this->weather = new Weather($weatherFixture);

        $this->assertFalse($this->weather->isUpdateAvailable());
    }
}
