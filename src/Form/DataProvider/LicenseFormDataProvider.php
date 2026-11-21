<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

namespace Onlineshopmodule\PrestaShop\Module\Vatnumber\Form\DataProvider;

use Onlineshopmodule\PrestaShop\Module\Vatnumber\Module\ConfigurationAdapter;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class LicenseFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        protected ConfigurationAdapter $configurationAdapter,
        protected \GC_Vatnumber $module
    ) {
    }

    public function getData()
    {
        return [
            'license' => trim($this->configurationAdapter->getGlobal('LICENSE')),
            'privacy' => (bool) $this->configurationAdapter->getGlobal('PRIVACY'),
        ];
    }

    public function setData(array $data)
    {
        $license = trim($data['license']);

        $this->configurationAdapter->setGlobal('LICENSE', $license);
        $this->configurationAdapter->setGlobal('PRIVACY', (bool) $data['privacy']);

        $this->module->registerLicense($license);

        return [];
    }
}
