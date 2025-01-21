<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Model;

use Bejblade\OpenWeather\OpenWeatherDate;

class AirPollution
{
    /** @var OpenWeatherDate Date and time of last data calculation */
    private OpenWeatherDate $lastUpdated;

    /** @var int Air Quality Index. Possible values: 1, 2, 3, 4, 5. Where 1 = Good, 5 = Very Poor */
    private int $aqi;

    /** @var float Сoncentration of CO (Carbon monoxide), μg/m3 */
    private float $co;

    /** @var float Сoncentration of NO (Nitrogen monoxide), μg/m3 */
    private float $no;

    /** @var float Сoncentration of NO2 (Carbon dioxide), μg/m3 */
    private float $no2;

    /** @var float Сoncentration of O3 (Ozone), μg/m3 */
    private float $o3;

    /** @var float Сoncentration of SO2 (Sulphur dioxide), μg/m3 */
    private float $so2;

    /** @var float Сoncentration of PM2.5 (Fine particles matter), μg/m3 */
    private float $pm2_5;

    /** @var float Сoncentration of PM10 (Coarse particulate matter), μg/m3 */
    private float $pm10;

    /** @var float Сoncentration of NH3 (Ammonia), μg/m3 */
    private float $nh3;
    public function __construct(array $data)
    {
        $this->lastUpdated = new OpenWeatherDate("@{$data['dt']}");
        $this->aqi = $data['main']['aqi'];
        $this->co = $data['components']['co'];
        $this->no = $data['components']['no'];
        $this->no2 = $data['components']['no2'];
        $this->o3 = $data['components']['o3'];
        $this->so2 = $data['components']['so2'];
        $this->pm2_5 = $data['components']['pm2_5'];
        $this->pm10 = $data['components']['pm10'];
        $this->nh3 = $data['components']['nh3'];
    }

    /**
     * Get last update time object
     * @return OpenWeatherDate
     */
    public function getLastUpdated(): OpenWeatherDate
    {
        return $this->lastUpdated;
    }

    /**
     * Get formatted date and time of last data calculation
     * @return string
     */
    public function getLastUpdateTime(): string
    {
        return $this->lastUpdated->getFormatted();
    }

    /**
     * Get Air Quality Index
     * @return int
     */
    public function getAirQualityIndex(): int
    {
        return $this->aqi;
    }

    /**
     * Get description of current Air Quality Index
     * @return string
     */
    public function getAirQualityIndexDescription(): string
    {
        return $this->getAirQualityDescription($this->aqi);
    }

    /**
     * Get description for given air pollutant or air quality index
     * @param int $quality Quality of air pollutant or air quality index
     * @return string
     */
    private function getAirQualityDescription(int $quality): string
    {
        return match ($quality) {
            1 => 'Good',
            2 => 'Fair',
            3 => 'Moderate',
            4 => 'Poor',
            5 => 'Very Poor',
        };
    }

    /**
     * Get Carbon Monoxide concentration in μg/m3
     * @return float
     */
    public function getCarbonMonoxide(): float
    {
        return $this->co;
    }

    /**
     * Get description for carbon monoxide concentraction
     * @return string
     */
    public function getCarbonMonoxideDescription(): string
    {
        $quality = match (true) {
            $this->co >= 0 && $this->co < 4400 => 1,
            $this->co >= 4400 && $this->co < 9400 => 2,
            $this->co >= 9400 && $this->co < 12400 => 3,
            $this->co >= 12400 && $this->co < 15400 => 4,
            $this->co >= 15400 => 5,
        };

        return $this->getAirQualityDescription($quality);
    }

    /**
     * Get description for nitrogen dioxide concentraction
     * @return string
     */
    public function getNitrogenDioxideDescription(): string
    {
        $quality = match (true) {
            $this->no2 >= 0 && $this->no2 < 40 => 1,
            $this->no2 >= 40 && $this->no2 < 70 => 2,
            $this->no2 >= 70 && $this->no2 < 150 => 3,
            $this->no2 >= 150 && $this->no2 < 200 => 4,
            $this->no2 >= 200 => 5,
        };

        return $this->getAirQualityDescription($quality);
    }

    /**
     * Get description for ozone concentraction
     * @return string
     */
    public function getOzoneDescription(): string
    {
        $quality = match (true) {
            $this->o3 >= 0 && $this->o3 < 60 => 1,
            $this->o3 >= 60 && $this->o3 < 100 => 2,
            $this->o3 >= 100 && $this->o3 < 140 => 3,
            $this->o3 >= 140 && $this->o3 < 180 => 4,
            $this->o3 >= 180 => 5,
        };

        return $this->getAirQualityDescription($quality);
    }

    /**
     * Get description for sulphur dioxide concentraction
     * @return string
     */
    public function getSulphurDioxideDescription(): string
    {
        $quality = match (true) {
            $this->so2 >= 0 && $this->so2 < 20 => 1,
            $this->so2 >= 20 && $this->so2 < 80 => 2,
            $this->so2 >= 80 && $this->so2 < 250 => 3,
            $this->so2 >= 250 && $this->so2 < 350 => 4,
            $this->so2 >= 350 => 5,
        };

        return $this->getAirQualityDescription($quality);
    }

    /**
     * Get description for fine particles concentraction
     * @return string
     */
    public function getFineParticlesMatterDescription(): string
    {
        $quality = match (true) {
            $this->pm2_5 >= 0 && $this->pm2_5 < 10 => 1,
            $this->pm2_5 >= 10 && $this->pm2_5 < 25 => 2,
            $this->pm2_5 >= 25 && $this->pm2_5 < 50 => 3,
            $this->pm2_5 >= 50 && $this->pm2_5 < 75 => 4,
            $this->pm2_5 >= 75 => 5,
        };

        return $this->getAirQualityDescription($quality);
    }

    /**
     * Get description for coarse particles concentraction
     * @return string
     */
    public function getCoarseParticulateMatterDescription(): string
    {
        $quality = match (true) {
            $this->pm10 >= 0 && $this->pm10 < 20 => 1,
            $this->pm10 >= 20 && $this->pm10 < 50 => 2,
            $this->pm10 >= 50 && $this->pm10 < 100 => 3,
            $this->pm10 >= 100 && $this->pm10 < 200 => 4,
            $this->pm10 >= 200 => 5,
        };

        return $this->getAirQualityDescription($quality);
    }

    /**
     * Get Nitrogen Monoxide concentration in μg/m3
     * @return float
     */
    public function getNitrogenMonoxide(): float
    {
        return $this->no;
    }

    /**
     * Get Nitrogen Dioxide concentration in μg/m3
     * @return float
     */
    public function getNitrogenDioxide(): float
    {
        return $this->no2;
    }

    /**
     * Get Ozone concentration in μg/m3
     * @return float
     */
    public function getOzone(): float
    {
        return $this->o3;
    }

    /**
     * Get Sulphur Dioxide concentration in μg/m3
     * @return float
     */
    public function getSulphurDioxide(): float
    {
        return $this->so2;
    }

    /**
     * Get Fine particles matter concentration in μg/m3
     * @return float
     */
    public function getFineParticlesMatter(): float
    {
        return $this->pm2_5;
    }

    /**
     * Get Coarse particles matter concentration in μg/m3
     * @return float
     */
    public function getCoarseParticulateMatter(): float
    {
        return $this->pm10;
    }

    /**
     * Get Ammonia concentraction in μg/m3
     * @return float
     */
    public function getAmmonia(): float
    {
        return $this->nh3;
    }

    /**
     * Get all air pollution data
     * @return array
     */
    public function all(): array
    {
        return [
            $this->co,
            $this->no,
            $this->no2,
            $this->o3,
            $this->so2,
            $this->pm2_5,
            $this->pm10,
            $this->nh3
        ];
    }
}
