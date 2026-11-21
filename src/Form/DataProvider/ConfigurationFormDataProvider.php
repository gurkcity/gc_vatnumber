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

use Onlineshopmodule\PrestaShop\Module\Vatnumber\Form\AbstractFormDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class ConfigurationFormDataProvider extends AbstractFormDataProvider implements FormDataProviderInterface
{
    public function getData()
    {
        return [
            'js_defer' => $this->configurationAdapter->get('JS_DEFER'),
            'theme' => $this->configurationAdapter->get('THEME'),
            'home_country' => json_decode($this->configurationAdapter->get('HOME_COUNTRY')),
            'eu_countries' => json_decode($this->configurationAdapter->get('EU_COUNTRIES')),
        ];
    }

    public function setData(array $data)
    {
        if (isset($data['js_defer'])) {
            $this->updateConfiguration('JS_DEFER', 'js_defer', $data);
        }

        if (isset($data['theme'])) {
            $this->updateConfiguration('THEME', 'theme', $data);
        }

        if (isset($data['home_country'])) {
            $data['home_country'] = json_encode($data['home_country']);
            $this->updateConfiguration('HOME_COUNTRY', 'home_country', $data);
        }

        if (isset($data['eu_countries'])) {
            $data['eu_countries'] = json_encode($data['eu_countries']);
            $this->updateConfiguration('EU_COUNTRIES', 'eu_countries', $data);
        }

        return [];
    }
}
