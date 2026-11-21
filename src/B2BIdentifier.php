<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

/**
 * 1. Fall: Rechnung inkl. Mwst
 * B2C Kunde in DE und EU
 * B2B Kunde ohne Vat in DE
 *
 * 2. Fall: Produkte Netto, Summe zzgl. MwSt
 * B2B Kunde ohne VAT in EU
 * B2B Kunde mit VAT aus Welt
 *
 * 3. Fall Nettorechung
 * Kunde aus Welt
 * B2B mit Vat aus EU
 */


namespace Onlineshopmodule\PrestaShop\Module\Vatnumber;

use Address;
use Country;
use Validate;

class B2BIdentifier
{
    const TYPE_WITH_TAX = 0;
    const TYPE_TAX_IN_TOTAL = 1;
    const TYPE_TAX_EXCL = 2;

    private $address = null;

    /** @var array */
    private $home_country = [];

    /** @var array */
    private $eu_countries = [];

    public function __construct(
        Address $address,
        array $home_country,
        array $eu_countries
    ) {
        $this->address = $address;
        $this->home_country = $home_country;
        $this->eu_countries = $eu_countries;
    }

    public function getB2BType(): int
    {
        if (
            $this->isNotHomeAndNotEu()
            || $this->isCompanyWithVatInEu()
        ) {
            return self::TYPE_TAX_EXCL;
        } elseif (
            $this->isCompanyWithVatInHome()
            || $this->isCompanyWithoutVatInEu()
            || $this->isCompanyWithVatInNotHomeAndNotEu()
        ) {
            return self::TYPE_TAX_IN_TOTAL;
        }

        return self::TYPE_WITH_TAX;
    }

    private function isHomeCountry(): bool
    {
        $country = $this->getAddressCountry();

        if (!Validate::isLoadedObject($country)) {
            return false;
        }

        return in_array($country->id, $this->home_country);
    }

    private function isEuCountry(): bool
    {
        $country = $this->getAddressCountry();

        if (!Validate::isLoadedObject($country)) {
            return false;
        }

        return in_array($country->id, $this->eu_countries);
    }

    private function isNotHomeAndNotEu(): bool
    {
        return !$this->isHomeCountry() && !$this->isEuCountry();
    }

    private function isCompanyWithVatInHome(): bool
    {
        return $this->isCompany() && $this->hasVat() && $this->isHomeCountry();
    }

    private function isCompanyWithVatInEu(): bool
    {
        return $this->isCompany() && $this->hasVat() && $this->isEuCountry();
    }

    private function isCompanyWithoutVatInEu(): bool
    {
        return $this->isCompany() && !$this->hasVat() && $this->isEuCountry();
    }

    private function isCompanyWithVatInNotHomeAndNotEu(): bool
    {
        return $this->isCompany() && $this->hasVat() && $this->isNotHomeAndNotEu();
    }

    private function isCompany(): bool
    {
        return '' !== $this->address->company;
    }

    private function isVatValid(string $vat): bool
    {
        return '' !== $vat;
    }

    private function hasVat(): bool
    {
        return '' !== $this->address->vat_number;
    }

    private function getAddressCountry(): Country
    {
        return new Country((int)$this->address->id_country);
    }
}
