<?php

declare(strict_types=1);

namespace Bejblade\OpenWeather\Interface;

use Bejblade\OpenWeather\Entity\Location;

/**
 * Endpoint interface for calling API with Location objects
 */
interface LocationAwareEndpointInterface extends EndpointInterface
{
    /**
     * Make a call to API endpoint using Location object
     * @param Location $location Location object which will be used to fetch API data
     * @param array $params Parameters to use in call
     * @return mixed
     */
    public function callWithLocation(Location $location, array $params = []);
}
