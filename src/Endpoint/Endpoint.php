<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint;

use Bejblade\OpenWeather\Interface\EndpointInterface;
use Bejblade\OpenWeather\OpenWeatherClient;
use Bejblade\OpenWeather\Config;

/**
 * Endpoint class which calls API endpoint
 */
abstract class Endpoint implements EndpointInterface
{
    /**
     * API version to use in endpoint request
     * @var string
     */
    protected string $apiVersion;

    /**
     * Language in which API data will be fetched
     * @var string
     */
    protected string $language;

    /**
     * Units format in which API data will be fetched
     * @var string
     */
    protected string $units;

    /**
     * Client used to call API
     * @var OpenWeatherClient
     */
    protected OpenWeatherClient $client;

    public function __construct(OpenWeatherClient $client)
    {
        $this->client = $client;
        $this->configure();
    }

    /**
     * Return response from API as array
     * @param array $params Parameters to use in API request
     * @return array
     */
    protected function getResponse(array $params = []): array
    {
        $this->validate($params);
        return $this->client->callApi($this->buildUrl(), ['query' => $params]);
    }

    abstract public function call(array $params = []);

    abstract public function getEndpoint(): string;

    /**
     * Returns array of available options to use in request
     * @return array
     */
    abstract protected function getAvailableOptions(): array;

    /**
     * Build URL which will be used to make API request
     * @return string
     */
    protected function buildUrl(): string
    {
        return 'data' . '/' . $this->apiVersion . '/' . $this->getEndpoint();
    }

    /**
     * Set configuration for endpoint
     * @return void
     */
    protected function configure(): void
    {
        $this->apiVersion = Config::configuration()->get('api_version');
        $this->language = Config::configuration()->get('language');
        $this->units = Config::configuration()->get('units');
    }

    /**
     * Validate that parameteres provided are supported and that they are correctly set
     * @param array $params Parameters to validate
     * @return void
     */
    protected function validate(array $params): void
    {
        $this->validateOptionsSupport($params);
    }

    /**
     * Validate that parameters provided are supported by current endpoint
     * @param array $params Parameters to validate
     * @throws \InvalidArgumentException When parameters are not supported
     * @return bool
     */
    protected function validateOptionsSupport(array $params): bool
    {
        $invalidParams = array_diff(array_keys($params), $this->getAvailableOptions());
        if (!empty($invalidParams)) {
            throw new \InvalidArgumentException('Parameters not supported: ' . implode($invalidParams));
        }

        return true;
    }
}
