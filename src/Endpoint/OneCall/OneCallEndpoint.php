<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Endpoint\OneCall;

use Bejblade\OpenWeather\Endpoint\Endpoint;
use Bejblade\OpenWeather\Interface\LocationAwareEndpointInterface;
use Bejblade\OpenWeather\Config;

/**
 * Abstract parent class for all OneCall endpoints
 */
abstract class OneCallEndpoint extends Endpoint implements LocationAwareEndpointInterface
{
    public function getEndpoint(): string
    {
        return 'onecall';
    }

    protected function configure(): void
    {
        parent::configure();

        $this->apiVersion = Config::configuration()->get('one_call_api_version');
    }
}
