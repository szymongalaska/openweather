<?php

namespace Bejblade\OpenWeather;

use Bejblade\OpenWeather\Exception\OpenWeatherClientException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class OpenWeatherClient extends Client
{
    /**
     * Array with default query options
     * @var array
     */
    private array $defaultOptions = [];

    public function __construct(array $config = [])
    {
        if ($config['query']) {
            $this->defaultOptions = ['query' => $config['query']];
        }

        unset($config['query']);
        parent::__construct($config);
    }

    /**
     * Send request to API and parse its response
     * @param string $url Request url
     * @param array $options Request options to apply
     * @throws OpenWeatherClientException
     * @return array
     */
    public function callApi(string $url, array $options = []): array
    {
        $options = array_merge_recursive($options, $this->defaultOptions);

        try {
            $response = $this->get($url, $options);
            return $this->parseResponse($response);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new OpenWeatherClientException($e->getMessage(), $e->getRequest(), $e->getResponse());
        }
    }

    /**
     * Parse response to array
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    private function parseResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->__tostring(), true);
    }
}
