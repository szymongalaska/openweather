<?php

namespace Tests;

use Bejblade\OpenWeather\OpenWeatherClient;
use GuzzleHttp\Psr7\Response;

class OpenWeatherClientTest extends BaseTestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->getMockBuilder(OpenWeatherClient::class)
            ->setConstructorArgs([
                'config' => [
                    'base_uri' => 'https://test_base_uri',
                    'query' => [
                        'appid' => 'test_app_id',
                        'lang' => 'en'
                    ],
                ],
            ])
            ->onlyMethods(['get'])
            ->getMock();

    }

    protected function tearDown(): void
    {
        unset($this->client);
        parent::tearDown();
    }

    public function test_call_api_returns_data(): void
    {
        $response = new Response(200, [], json_encode(['data' => 'test']));

        $this->client->expects($this->once())
            ->method('get')
            ->with('weather', ['query' => ['appid' => 'test_app_id', 'lang' => 'en', 'q' => 'London']])
            ->willReturn($response);

        $actual = $this->client->callApi('weather', ['query' => ['q' => 'London']]);

        $this->assertSame(['data' => 'test'], $actual);
    }
}
