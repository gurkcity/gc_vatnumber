<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

namespace Onlineshopmodule\PrestaShop\Module\Vatnumber\Log;

use Onlineshopmodule\PrestaShop\Module\Vatnumber\Module\ConfigurationAdapter;

class LogLevel
{
    private $configurationAdapter;

    public function __construct(ConfigurationAdapter $configurationAdapter)
    {
        $this->configurationAdapter = $configurationAdapter;
    }

    public function get(): int
    {
        return (int) $this->configurationAdapter->getGlobal('LOG_LEVEL');
    }

    public function set(int $level): bool
    {
        return (bool) $this->configurationAdapter->setGlobal('LOG_LEVEL', $level);
    }
}
