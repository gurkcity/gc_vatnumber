<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

namespace Onlineshopmodule\PrestaShop\Module\Vatnumber\Traits;

trait ModuleHookTrait
{
    public function hookTaxManager($args)
    {
        return parent::hookTaxManager($args);
    }

    public function hookActionValidateOrder($params)
    {
        if (
            empty($params['order'])
            || !($params['order'] instanceof \Order)
        ) {
            return;
        }

        $this->updateOrder($params['order']);
    }

    public function hookActionFrontControllerSetVariables($params)
    {
        try {
            $idAddress = (int) \Context::getContext()->cart->{\Configuration::get('PS_TAX_ADDRESS_TYPE')};
            $address = new \Address($idAddress);

            $isB2b = false;

            if ($address instanceof \Address && \Validate::isLoadedObject($address)) {
                $isB2b = \VATNumberTaxManager::isAvailableForThisAddress($address);
            }
        } catch (\Exception $e) {
            return [];
        }

        return [
            'is_b2b' => $isB2b,
        ];
    }
}
