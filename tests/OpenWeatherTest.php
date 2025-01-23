<?php

namespace Tests;

use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\OpenWeather;
use Bejblade\OpenWeather\Model\Location;
use Bejblade\OpenWeather\Model\Weather;
use Bejblade\OpenWeather\Model\Forecast;
use Bejblade\OpenWeather\Model\AirPollution;

class OpenWeatherTest extends BaseTestCase
{
    protected $openWeather;

    protected $endpointMock;

    protected $location;

    protected $forecast;

    protected $weather;

    protected $airPollution;

    public function setUp(): void
    {
        parent::setUp();
        $this->openWeather = $this->getMockBuilder(OpenWeather::class)
            ->setConstructorArgs(['config' => ['api_key' => 'testApiKey']])
            ->onlyMethods(['getEndpoint'])
            ->getMock();

        $this->endpointMock = $this->createMock(LocationAwareEndpointInterface::class);
        $this->location = new Location($this->fixture('location'));
        $this->weather = new Weather($this->fixture('weather'));
        $this->forecast = new Forecast($this->fixture('forecast')['list']);
        $this->airPollution = new AirPollution($this->fixture('air_pollution'));
    }

    public function tearDown(): void
    {
        unset($this->openWeather);
        unset($this->endpointMock);
        unset($this->location);
        unset($this->weather);

        parent::tearDown();
    }

    public function test_find_location_by_name_returns_locations(): void
    {
        $this->endpointMock->expects($this->once())->method('call')->with(['q' => 'London', 'limit' => 1])->willReturn([$this->location]);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('geo.direct')->willReturn($this->endpointMock);

        $actual = $this->openWeather->findLocationByName('London');
        $this->assertSame($this->location, $actual);
    }

    public function test_find_location_by_zip_code_returns_location(): void
    {
        $this->endpointMock->expects($this->once())->method('call')->with(['zip' => 'E14,GB'])->willReturn($this->location);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('geo.zip')->willReturn($this->endpointMock);

        $actual = $this->openWeather->findLocationByZipCode('E14', 'GB');
        $this->assertSame($this->location, $actual);
    }
    public function test_find_location_by_coords_returns_locations(): void
    {
        $this->endpointMock->expects($this->once())->method('call')->with(['lat' => '51.5156177', 'lon' => '-0.0919983', 'limit' => 1])->willReturn([$this->location]);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('geo.reverse')->willReturn($this->endpointMock);

        $actual = $this->openWeather->findLocationByCoords('51.5156177', '-0.0919983')[0];
        $this->assertSame($this->location, $actual);
    }

    public function test_get_weather_by_location_returns_weather(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location)->willReturn($this->weather);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('weather')->willReturn($this->endpointMock);
        $this->location->setWeather($this->weather);

        $actual = $this->openWeather->getWeather($this->location);
        $this->assertSame($this->weather, $actual);
    }

    public function test_get_weather_by_location_skips_update_when_no_update_available(): void
    {
        $weatherFixture = $this->fixture('weather');
        $weatherFixture['dt'] = time();
        $this->weather = new Weather($weatherFixture);
        $this->location->setWeather($this->weather);

        $this->endpointMock->expects($this->never())->method('callWithLocation');
        $this->openWeather->expects($this->never())->method('getEndpoint');


        $actual = $this->openWeather->getWeather($this->location);

        $this->assertSame($this->weather, $actual);
    }

    public function test_get_current_weather_updates_weather(): void
    {
        $weatherFixture = $this->fixture('weather');
        $weatherFixture['dt'] -= 900;
        $weather = new Weather($weatherFixture);
        $this->location->setWeather($weather);

        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location)->willReturn($this->weather);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('weather')->willReturn($this->endpointMock);


        $actual = $this->openWeather->getWeather($this->location);
        $this->assertNotSame($weather, $actual);
    }

    public function test_get_forecast_by_location_returns_forecast(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location)->willReturn($this->forecast);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('forecast')->willReturn($this->endpointMock);


        $this->location->setForecast($this->forecast);

        $actual = $this->openWeather->getForecast($this->location);
        $this->assertSame($this->forecast, $actual);
    }

    public function test_get_air_pollution_returns_air_pollution(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location)->willReturn($this->airPollution);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('air.pollution')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getAirPollution($this->location);
        $this->assertSame($this->airPollution, $actual);
    }

    public function test_get_air_pollution_forecast_returns_array(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location)->willReturn([$this->airPollution]);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('air.forecast')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getAirPollutionForecast($this->location);
        $this->assertSame([$this->airPollution], $actual);
    }

    public function test_get_air_pollution_history_returns_array(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location)->willReturn([$this->airPollution]);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('air.history')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getAirPollutionHistory($this->location, time(), time());
        $this->assertSame([$this->airPollution], $actual);
    }

    public function test_get_one_call_weather_and_forecast_returns_current_weather_and_daily_forecast_data(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location)->willReturn(['current' => $this->weather, 'daily' => $this->forecast]);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('onecall')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getOneCallWeatherAndForecast($this->location);
        $this->assertSame(['current' => $this->weather, 'daily' => $this->forecast], $actual);
        $this->assertSame($this->location->weather(), $actual['current']);
        $this->assertSame($this->location->forecast(), $actual['daily']);
    }

    public function test_get_one_call_all_data_returns_all_data(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location)->willReturn(['current' => $this->weather, 'daily' => $this->forecast, 'hourly' => $this->forecast, 'minutely' => [], 'alerts' => []]);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('onecall')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getOneCallAllData($this->location);
        $this->assertSame(['current' => $this->weather, 'daily' => $this->forecast, 'hourly' => $this->forecast, 'minutely' => [], 'alerts' => []], $actual);
    }

    public function test_get_one_call_data_returns_specified_data(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location, ['exclude' => 'daily,hourly,minutely,alerts'])->willReturn(['current' => $this->weather]);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('onecall')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getOneCallData($this->location, 'current');
        $this->assertSame($this->weather, $actual);
    }

    public function test_get_one_call_data_except_returns_specified_data(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location, ['exclude' => 'daily,hourly,minutely,alerts'])->willReturn(['current' => $this->weather]);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('onecall')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getOneCallDataExcept($this->location, 'daily,hourly,minutely,alerts');
        $this->assertSame(['current' => $this->weather], $actual);
    }

    public function test_get_weather_daily_aggregation_returns_aggregated_data(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location, ['date' => '2024-09-18'])->willReturn($this->weather);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('onecall.aggregation')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getWeatherDailyAggregation($this->location, '2024-09-18');
        $this->assertSame($this->weather, $actual);
        $this->assertSame('2024-09-18', $actual->getTimestamp()->format('Y-m-d'));
    }

    public function test_get_weather_timemachine_returns_weather_data(): void
    {
        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location, ['dt' => '1726660758'])->willReturn($this->weather);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('onecall.timemachine')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getWeatherTimeMachine($this->location, '1726660758');
        $this->assertSame($this->weather, $actual);
        $this->assertSame(1726660758, $actual->getTimestamp()->getTimestamp());
    }

    public function test_get_weather_overview_returns_string(): void
    {
        $overview = "The current weather is overcast with a 
            temperature of 16°C and a feels-like temperature of 16°C. 
            The wind speed is 4 meter/sec with gusts up to 6 meter/sec 
            coming from the west-southwest direction. 
            The air pressure is at 1007 hPa with a humidity level of 79%. 
            The dew point is at 12°C and the visibility is 10000 meters. 
            The UV index is at 4, indicating moderate risk from the 
            sun's UV rays. 
            The sky is covered with overcast clouds, and there is 
            no precipitation expected at the moment. 
            Overall, it is a moderately cool and cloudy day 
            with light to moderate winds from the west-southwest.";

        $this->endpointMock->expects($this->once())->method('callWithLocation')->with($this->location)->willReturn($overview);
        $this->openWeather->expects($this->once())->method('getEndpoint')->with('onecall.overview')->willReturn($this->endpointMock);

        $actual = $this->openWeather->getWeatherOverview($this->location);
        $this->assertSame($overview, $actual);
    }
}
