<?php

return [
    'api_key' => getenv('OPENWEATHER_API_KEY') ?: null,
    'api_version' => '2.5',
    'geo_api_version' => '1.0',
    'language' => getenv('OPENWEATHER_LANGUAGE') ?: 'en',
    'date_format' => 'd/m/Y',
    'time_format' => 'h:i A',
    'day_format' => 'l',
    'temperature' => 'c',
    'timezone' => 'UTC',
    'url' => 'https://api.openweathermap.org/',
];
