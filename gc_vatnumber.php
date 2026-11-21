<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

use Onlineshopmodule\PrestaShop\Module\Vatnumber\Traits\ModuleFunctionsTrait;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Traits\ModuleHelperTrait;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Traits\ModuleHookTrait;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Traits\ModuleLicenseTrait;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\Traits\ModuleTrait;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class GC_Vatnumber extends TaxManagerModule
{
    use ModuleTrait;
    use ModuleFunctionsTrait;
    use ModuleHelperTrait;
    use ModuleHookTrait;
    use ModuleLicenseTrait;

    public function __construct()
    {
        $this->version = '9.0.0';

        $this->name = 'gc_vatnumber';

        $this->author = 'Gurkcity';

        $this->ps_versions_compliancy = [
            'min' => '9.0.0',
            'max' => _PS_VERSION_,
        ];

        $this->tab = 'billing_invoicing';

        $this->displayName = $this->trans('GC European VAT number', [], 'Modules.Gcvatnumber.Admin');
        $this->displayNamePre = $this->trans('GC European', [], 'Modules.Gcvatnumber.Admin');
        $this->displayNamePost = $this->trans('VAT number', [], 'Modules.Gcvatnumber.Admin');
        $this->description = $this->trans('Enables you to enter the intra-community VAT number when creating the address. You must fill in the company field to allow entering the VAT number.', [], 'Modules.Gcvatnumber.Admin');
        $this->description_full = $this->trans('Enables you to enter the intra-community VAT number when creating the address. You must fill in the company field to allow entering the VAT number.', [], 'Modules.Gcvatnumber.Admin');

        parent::__construct();

        $this->initModule();
    }

    public function parentInitModule()
    {
        $this->tax_manager_class = 'VATNumberTaxManager';
    }
}
