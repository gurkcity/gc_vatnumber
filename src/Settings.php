<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

namespace Onlineshopmodule\PrestaShop\Module\Vatnumber;

use Doctrine\DBAL\Schema\Schema;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Module\AbstractSettings;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Settings\Config;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Settings\Controller;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Settings\Hook;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Settings\Sql;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Settings\Tab;

class Settings extends AbstractSettings
{
    public function config(): array
    {
        return [
            'MANAGEMENT' => new Config(
                'MANAGEMENT',
                0
            ),
            'HOME_COUNTRY' => new Config(
                'HOME_COUNTRY',
                '[]'
            ),
            'EU_COUNTRIES' => new Config(
                'EU_COUNTRIES',
                '[]'
            ),
        ];
    }

    public function controllers(): array
    {
        return [];
    }

    public function cron(): array
    {
        return [];
    }

    public function hooks(): array
    {
        return [
            'actionValidateOrder' => new Hook('actionValidateOrder'),
            'actionFrontControllerSetVariables' => new Hook('actionFrontControllerSetVariables'),
        ];
    }

    public function orderStates(): array
    {
        return [];
    }

    public function sql(): Sql
    {
        $schema = new Schema();

        return new Sql($schema);
    }

    public function tabs(): array
    {
        return [];
    }

    public function translations(): array
    {
        return [
            // Locale
            'de-DE' => [
                // Original, Translation, Domain
                ['Awaiting payment: Vatnumber', 'Warten auf Zahlungseingang: Vatnumber', 'ModulesGcvatnumberAdmin'],
                ['Order placed', 'Bestellung eingegangen', 'ModulesGcvatnumberAdmin'],
                ['Vatnumber Module', 'Vatnumber Modul', 'ModulesGcvatnumberAdmin'],
                ['Configuration', 'Einstellungen', 'ModulesGcvatnumberAdmin'],
                ['Cron', 'Cron', 'ModulesGcvatnumberAdmin'],
                ['Logs', 'Logs', 'ModulesGcvatnumberAdmin'],
                ['Maintenance', 'Wartung', 'ModulesGcvatnumberAdmin'],
                ['License', 'Lizenz', 'ModulesGcvatnumberAdmin'],
                ['Payment', 'Status & Zahlung', 'ModulesGcvatnumberAdmin'],
            ],
        ];
    }

    public function fixtures(): callable
    {
        return function () {
            return true;
        };
    }

    public function help(): string
    {
        return 'http://www.onlineshop-module.de/';
    }
}
