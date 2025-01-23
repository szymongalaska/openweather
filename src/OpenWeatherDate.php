<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather;

class OpenWeatherDate extends \DateTime
{
    /**
     * Configuration defined format of date to use
     * @var string
     */
    private string $dateFormat;

    public function __construct(string $datetime, int $timezone = 0)
    {
        $this->setDateFormat();
        if ($timezone == 0) {
            $timezone = Config::configuration()->get('timezone');
        } else {
            $timezone = $timezone / 3600;
            $timezone = $timezone > 0 ? '+' . $timezone : (string) $timezone;
        }

        $timezone = new \DateTimeZone($timezone);
        parent::__construct($datetime);
        $this->setTimezone($timezone);

    }

    /**
     * Set `dateFormat` according to configuration
     * @return void
     */
    private function setDateFormat(): void
    {
        $this->dateFormat = preg_replace(
            '/d|D|j|l|N|S|w|z/',
            Config::configuration()->get('day_format'),
            Config::configuration()->get('date_format') . ' ' . Config::configuration()->get('time_format')
        );
    }

    /**
     * Helper method that checks if this date is same as given date
     * @param \DateTimeInterface $date Date to compare
     * @return bool
     */
    public function isSameDay(\DateTimeInterface $date): bool
    {
        return $date->format('Y-m-d') == $this->format('Y-m-d');
    }

    /**
     * Get current date and time in configuration defined format
     * @return string
     */
    public function getFormatted(): string
    {
        return $this->format($this->dateFormat);
    }
}
