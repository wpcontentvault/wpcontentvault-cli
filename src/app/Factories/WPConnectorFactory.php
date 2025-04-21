<?php

declare(strict_types=1);

namespace App\Factories;

use WPAjaxConnector\WPAjaxConnectorPHP\WPConnector;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class WPConnectorFactory
{
    public function __construct() {}

    public function make(string $domain, string $apiKey): WPConnectorInterface
    {
        return new WPConnector($domain, $apiKey);
    }
}
