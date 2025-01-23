# OpenWeather API PHP Library

A PHP library for interacting with the OpenWeather API, providing tools for retrieving and processing weather data, including forecasts, current conditions, and historical data.

## Features

- Support for free OpenWeather API endpoints (including One Call API):
  - Current weather data
  - 5-day/3-hour forecast
  - One Call API (current, hourly, and daily forecast)
  - Historical weather data

## Basic setup

```php
use Bejblade\OpenWeather\OpenWeather;

// Initialize OpenWeather API with Your API key using default configuration
$api = new OpenWeather(['api_key' => 'your_api_key']);
```

## Example

```php
use Bejblade\OpenWeather\OpenWeather;


$api = new OpenWeather(['api_key' => 'your_api_key']);

// First initialize location object
$location = $api->findLocationByName('London');

// Fetch weather data
$weather = $api->getWeather($location);

echo 'Current weather in ' . $location->getName() .': '. $weather->getDescription();
echo 'Temperature: ' . $weather->temperature()->get().'°'.$weather->temperature()->getUnits();

if($location->getWeather()->isRaining()) // You can also access weather through location objects
    echo "It's raining";
```

## Installation

Install the library via Composer:

```bash
composer require bejblade/openweather
```

## Configuration

The `Config` class is used to manage global settings for the library. You can set following options:

- `api_key` (Required): Your OpenWeather API key.
- `language`: Language for API responses (default 'en').
- `date_format`: Date format used in dates of API data. PHP date format for displaying dates (default 'd/m/Y').
- `time_format`: Time format used in dates of API data. PHP date format for displaying time (default 'H:i').
- `timezone`: Some data from the API is returned only in the UTC timezone. Set this option to convert the data to your selected timezone. PHP supported timezone (default 'UTC')
- `units`: Units for temperature and measure, API data will be recieved in this format. (default metric)
  - `metric`: Celsius/Metric
  - `imperial`: Fahrenheit/Imperial
  - `standard`: Kelvin/Metric

Some of the options can be set in .env file:

- `api_key`: `OPENWEATHER_API_KEY`,
- `language`: `OPENWEATHER_LANGUAGE`

By default, only `api_key` is required, other options will be loaded with default settings.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request.

## License

This project is licensed under the MIT License.