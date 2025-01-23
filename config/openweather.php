<?php

return [
    'api_key' => getenv('OPENWEATHER_API_KEY') ?: null,
    'api_version' => '2.5',
    'geo_api_version' => '1.0',
    'one_call_api_version' => '3.0',
    'language' => getenv('OPENWEATHER_LANGUAGE') ?: 'en',
    'date_format' => 'd/m/Y',
    'time_format' => 'H:i',
    'day_format' => 'l',
    'units' => 'metric',
    'timezone' => 'UTC',
    'url' => 'https://api.openweathermap.org/',
];
