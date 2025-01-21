<?php

namespace Bejblade\OpenWeather\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception class for client
 */
class OpenWeatherClientException extends \GuzzleHttp\Exception\ClientException
{
    public function __construct(string $message = "", RequestInterface $request, ResponseInterface $response, \Throwable $previous = null)
    {
        parent::__construct($message, $request, $response, $previous);
    }
}
