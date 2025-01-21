<?php

namespace Bejblade\OpenWeather\Exception;

/**
 * Exception class for configuration
 */
class InvalidConfigurationException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
