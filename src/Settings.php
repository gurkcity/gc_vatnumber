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
        return [];

        /*
        return [
            'TEST_CONFIG' => new Config(
                'TEST_CONFIG',
                [
                    'en' => 'Value',
                    'de' => 'Wert',
                ]
            ),
            'TEST_CONFIG_ABC' => new Config(
                'TEST_CONFIG_ABC',
                1
            ),
            'TEST_CONFIG_ABC' => new Config(
                'TEST_CONFIG_ABC',
                'value'
            ),
        ];
        */
    }

    public function controllers(): array
    {
        return [];

        /*
        return [
            new Controller('controller_name'),
        ];
        */
    }

    public function cron(): array
    {
        return [];

        /*
        return [
            'cronMethodName' => [
                'title' => $this->translator->trans('Name of the cronjob', [], 'Modules.Gcvatnumber.Admin'),
                'description' => $this->translator->trans('This cron job does super cool things in your webshop.', [], 'Modules.Gcvatnumber.Admin'),
                'use_queue' => true,
                'params' => [
                    [
                        'description' => $this->translator->trans('Description for parameter', [], 'Modules.Gcvatnumber.Admin'),
                        'title' => 'Parameter 1',
                        'name' => 'parameter',
                        'values' => [
                            '1' => $this->translator->trans('Parameter 1 value description', [], 'Modules.Gcvatnumber.Admin'),
                            '2' => $this->translator->trans('Parameter 2 value description', [], 'Modules.Gcvatnumber.Admin'),
                        ],
                    ],
                ],
                'command' => [
                    'arguments' => [
                        'action' => 'actionName',
                    ],
                    'cli_only' => true,
                ],
            ],
        ];
        */
    }

    public function hooks(): array
    {
        return [];

        /*
        return [
            'actionTestHook' => new Hook('actionTestHook'),
        ];
        */
    }

    public function orderStates(): array
    {
        return [];
    }

    public function sql(): Sql
    {
        $schema = new Schema();

        /*
        $table = $schema->createTable($this->dbPrefix . 'example');
        $table->addColumn('id_example', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('id_column', 'integer', ['unsigned' => true]);
        $table->addColumn('date_add', 'datetime');
        $table->addColumn('date_upd', 'datetime');
        $table->addColumn('active', 'integer', ['unsigned' => true]);
        $table->addColumn('deleted', 'integer', ['unsigned' => true]);
        $table->setPrimaryKey(['id_example']);
        $table->addIndex(['id_column']);

        $table = $schema->createTable($this->dbPrefix . 'example_lang');
        $table->addColumn('id_example', 'integer', ['unsigned' => true]);
        $table->addColumn('id_lang', 'integer', ['unsigned' => true]);
        $table->addColumn('name', 'string', ['length' => 64]);
        $table->addUniqueIndex(['id_example', 'id_lang']);

        $table = $schema->createTable($this->dbPrefix . 'example_shop');
        $table->addColumn('id_example', 'integer', ['unsigned' => true]);
        $table->addColumn('id_shop', 'integer', ['unsigned' => true]);
        $table->addColumn('active', 'integer', ['unsigned' => true]);
        $table->addUniqueIndex(['id_example', 'id_shop']);
        */

        return new Sql($schema);
    }

    public function tabs(): array
    {
        return [];

        /*
        return [
            new Tab(
                [
                    'en' => 'Example',
                    'de' => 'Example',
                ],
                'GcVatnumberExampleAdminController',
                'GcVatnumberConfigurationAdminParentController',
                'gc_vatnumber_examplegrid_index',
                '',
                'Example',
                'Modules.Gcvatnumber.Admin',
                true
            ),
        ];
        */
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
