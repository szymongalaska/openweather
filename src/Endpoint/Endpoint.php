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
     * @param array $options Options to use in API request
     * @return array
     */
    protected function getResponse(array $options = []): array
    {
        $this->validate($options);
        return $this->client->callApi($this->buildUrl(), ['query' => $options]);
    }

    abstract public function call(array $options = []);

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

    protected function configure(): void
    {
        $this->apiVersion = Config::configuration()->get('api_version');
        $this->language = Config::configuration()->get('language');
        $this->units = Config::configuration()->get('units');
    }

    /**
     * Validate options provided are supported or are correctly set
     * @param array $options
     * @return void
     */
    protected function validate(array $options): void
    {
        $this->validateOptionsSupport($options);
    }

    /**
     * Validate that options provided are supported by current endpoint
     * @param array $options
     * @throws \InvalidArgumentException
     * @return bool
     */
    protected function validateOptionsSupport(array $options): bool
    {
        $invalidOptions = array_diff(array_keys($options), $this->getAvailableOptions());
        if (!empty($invalidOptions)) {
            throw new \InvalidArgumentException('Options not supported: ' . implode($invalidOptions));
        }

        return true;
    }
}
