<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_1($module)
{
    $result = true;

    try {
        Configuration::updateValue('VATNUMBER_ZONE_1', json_encode([(int) Configuration::get('VATNUMBER_ZONE_1')]));
        Configuration::updateValue('VATNUMBER_ZONE_2', json_encode([(int) Configuration::get('VATNUMBER_ZONE_2')]));
    } catch (Exception $e) {
        $result &= false;
    }

    return (bool) $result;
}
