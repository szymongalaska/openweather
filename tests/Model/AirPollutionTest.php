<?php

namespace Tests;

use Bejblade\OpenWeather\Model\AirPollution;

class AirPollutionTest extends BaseTestCase
{
    protected $airPollution;
    protected function setUp(): void
    {
        parent::setUp();
        $this->airPollution = new AirPollution($this->fixture('air_pollution'));
    }

    protected function tearDown(): void
    {
        unset($this->airPollution);
        parent::tearDown();
    }

    public function test_get_air_quality_index_description_returns_proper_string(): void
    {
        $expected = 'Good';
        $this->assertSame($expected, $this->airPollution->getAirQualityIndexDescription());
    }

    public function test_get_carbon_monoxide_description_returns_proper_string(): void
    {
        $expected = 'Good';
        $this->assertSame($expected, $this->airPollution->getCarbonMonoxideDescription());
    }

    public function test_get_nitrogen_monoxide_description_returns_proper_string(): void
    {
        $expected = 'Good';
        $this->assertSame($expected, $this->airPollution->getNitrogenDioxideDescription());
    }

    public function test_get_ozone_description_returns_proper_string(): void
    {
        $expected = 'Fair';
        $this->assertSame($expected, $this->airPollution->getOzoneDescription());
    }

    public function test_get_sulphur_dioxide_returns_proper_string(): void
    {
        $expected = 'Good';
        $this->assertSame($expected, $this->airPollution->getSulphurDioxideDescription());
    }

    public function test_get_fine_particles_matter_returns_proper_string(): void
    {
        $expected = 'Good';
        $this->assertSame($expected, $this->airPollution->getFineParticlesMatterDescription());
    }

    public function test_get_coarse_particulate_matter_returns_proper_string(): void
    {
        $expected = 'Good';
        $this->assertSame($expected, $this->airPollution->getCoarseParticulateMatterDescription());
    }
}
