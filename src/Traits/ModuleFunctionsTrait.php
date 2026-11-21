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

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Operation\Rounding;

trait ModuleFunctionsTrait
{
    public function updateOrder(\Order $order)
    {
        $idAddress = $order->{\Configuration::get('PS_TAX_ADDRESS_TYPE')};

        $address = new \Address($idAddress);

        if (\VATNumberTaxManager::isAvailableForThisAddress($address)) {
            $this->copyTaxInclToTaxExclInOrder($order);
        } else {
            $this->correctTaxExclValuesToPrecision($order, 2);
        }
    }

    public function copyTaxInclToTaxExclInOrder(\Order $order)
    {
        \DB::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'order_detail`
            SET
                `unit_price_tax_excl` = `unit_price_tax_incl`,
                `total_price_tax_excl` = `total_price_tax_incl`
            WHERE
                `id_order` = ' . (int) $order->id . '
        ');

        \DB::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'orders`
            SET
                `total_products` = `total_products_wt`,
                `total_shipping_tax_excl` = `total_shipping_tax_incl`,
                `total_shipping` = `total_shipping_tax_incl`,
                `total_paid_tax_excl` = `total_paid_tax_incl`,
                `total_paid` = `total_paid_tax_incl`
            WHERE
                `id_order` = ' . (int) $order->id . '
        ');

        $order->total_shipping_tax_incl = (new DecimalNumber((string)$order->total_shipping_tax_incl))->toPrecision(2, Rounding::ROUND_HALF_EVEN);

        $order->total_products = $order->total_products_wt;
        $order->total_shipping_tax_excl = $order->total_shipping_tax_incl;
        $order->total_shipping = $order->total_shipping_tax_incl;
        $order->total_paid_tax_excl = $order->total_paid_tax_incl;
        $order->total_paid = $order->total_paid_tax_incl;
    }

    public function correctTaxExclValuesToPrecision(\Order $order, int $precision = 6)
    {
        $order->total_shipping_tax_incl = (new DecimalNumber((string)$order->total_shipping_tax_incl))->toPrecision($precision, Rounding::ROUND_HALF_EVEN);
        $order->total_shipping = $order->total_shipping_tax_incl;
    }
}
