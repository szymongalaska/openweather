<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Interface;

use Bejblade\OpenWeather\Model\Location;

/**
 * Endpoint interface for calling API with Location objects
 */
interface LocationAwareEndpointInterface extends EndpointInterface
{
    /**
     * Make a call to API endpoint using Location object
     * @param Location $location
     * @param array $options Parameters to use in call
     * @return mixed
     */
    public function callWithLocation(Location $location, array $options = []);
}
