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
     * Get Air Quality Index description
     * @return string
     */
    public function getAirQualityIndexDescription(): string
    {
        return match ($this->aqi) {
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
