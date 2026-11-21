<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

use Onlineshopmodule\PrestaShop\Module\Vatnumber\Maintenance\Maintenance;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param GC_Vatnumber $module
 *
 * @return bool
 */
function upgrade_module_9_0_2($module)
{
    try {
        $result = true;

        if (!Module::isEnabled($module->name)) {
            return false;
        }

        /**
         * @var Maintenance $maintenance
         */
        $maintenance = $module->get('onlineshopmodule.module.vatnumber.maintenance');

        $result = $maintenance->resetHooks();

        return (bool) $result;
    } catch (Exception $e) {
        /* @phpstan-ignore property.notFound */
        $module->getLogger()->upgrade->error($e->getMessage());

        return false;
    }
}
