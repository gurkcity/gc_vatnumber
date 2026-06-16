<?php

namespace Onlineshopmodule\PrestaShop\Module\Vatnumber;

use Address;

class B2BIdentifierFactory
{
    protected $id_zone_1 = 0;
    protected $id_zone_2 = 0;

    public function __construct(
        $id_zone_1,
        $id_zone_2
    ) {
        $this->id_zone_1 = $id_zone_1;
        $this->id_zone_2 = $id_zone_2;
        if (!is_array($this->id_zone_1)) {
            $this->id_zone_1 = [$this->id_zone_1];
        }
        if (!is_array($this->id_zone_2)) {
            $this->id_zone_2 = [$this->id_zone_2];
        }
    }

    public function getB2BIdentifierForAddress(Address $address): B2BIdentifier
    {
        if (!$this->id_zone_1 || !$this->id_zone_2) {
            throw new B2BIdentifierException('Zones are not set');
        }

        return new B2BIdentifier(
			$address,
			$this->id_zone_1,
			$this->id_zone_2
		);
    }
}
