<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Interface;

/**
 * Endpoint interface for calling API
 */
interface EndpointInterface
{
    /**
     * Make a call to API endpoint
     * @param array $params Parameters to use in call
     * @return mixed
     */
    public function call(array $params = []);

    /**
     * Get name of endpoint
     * @return string
     */
    public function getEndpoint(): string;
}
