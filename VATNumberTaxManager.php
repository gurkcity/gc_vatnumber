<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

use Onlineshopmodule\PrestaShop\Module\Vatnumber\B2BIdentifier;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\B2BIdentifierException;
use Onlineshopmodule\PrestaShop\Module\Vatnumber\B2BIdentifierFactory;

class VATNumberTaxManager implements TaxManagerInterface
{
	public static function isAvailableForThisAddress(Address $address)
	{
		/*
		HOTFIX

		For some reason, this check is called 6 times (?)

		1 w. the real address
		2 w.o. the real address

		1 w. the real address
		2 w.o. the real address

		=> [1 0 0 1 0 0]

		So we need to filter out the weird calls...

		We do this by caching the correct calls between calls;
		by creating a static variable, which we save the address to,
		if it does not contain NULL in some of the other fields.
		*/

		static $cached_address = null;

		if (!Validate::isLoadedObject($address)) {
			return false;
		}

		if ($address->id_customer) {
			$cached_address = $address;
		}

		if ($cached_address === null) {
			return false;
		}

		try {
			$b2b_identifier = (new B2BIdentifierFactory(
				json_decode(Configuration::get('GC_VATNUMBER_HOME_COUNTRY'), true),
				json_decode(Configuration::get('GC_VATNUMBER_EU_COUNTRIES'), true)
			))->getB2BIdentifierForAddress($cached_address);

			return $b2b_identifier->getB2BType() === B2BIdentifier::TYPE_TAX_EXCL;
		} catch (B2BIdentifierException $e) {

		}

		return false;
	}

	public function getTaxCalculator()
	{
		// If the address matches the european vat number criterias no taxes are applied
		$tax = new Tax();
		$tax->rate = 0;

		return new TaxCalculator([$tax]);
	}
}
