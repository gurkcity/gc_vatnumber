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

use Address;

class B2BIdentifierFactory
{
    protected $home_country = 0;
    protected $eu_countries = 0;

    public function __construct(
        $home_country,
        $eu_countries
    ) {
        $this->home_country = $home_country;
        $this->eu_countries = $eu_countries;

        if (!is_array($this->home_country)) {
            $this->home_country = [$this->home_country];
        }

        if (!is_array($this->eu_countries)) {
            $this->eu_countries = [$this->eu_countries];
        }
    }

    public function getB2BIdentifierForAddress(Address $address): B2BIdentifier
    {
        if (!$this->home_country || !$this->eu_countries) {
            throw new B2BIdentifierException('Countries are not set');
        }

        return new B2BIdentifier(
			$address,
			$this->home_country,
			$this->eu_countries
		);
    }
}
