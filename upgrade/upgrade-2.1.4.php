<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_4($module)
{
    $result = true;

    try {
        $zones_1 = json_decode((string) Configuration::get('VATNUMBER_ZONE_1'), true);
        if ($zones_1) {
            // Get home countries
            $home_country = Db::getInstance()->executeS('SELECT id_country FROM ' . _DB_PREFIX_ . 'country WHERE id_zone IN (' . implode(', ', array_map('intval', $zones_1)) . ')');

            // Set home countries
            Configuration::updateValue('VATNUMBER_HOME_COUNTRY', json_encode(array_map('intval', array_column($home_country, 'id_country'))));
        }

        $zones_2 = json_decode((string) Configuration::get('VATNUMBER_ZONE_2'), true);
        if ($zones_2) {
            // Get EU countries
            $home_country = Db::getInstance()->executeS('SELECT id_country FROM ' . _DB_PREFIX_ . 'country WHERE id_zone IN (' . implode(', ', array_map('intval', $zones_2)) . ')');

            // Set EU countries
            Configuration::updateValue('VATNUMBER_EU_COUNTRIES', json_encode(array_map('intval', array_column($home_country, 'id_country'))));
        }

        Configuration::deleteByName('VATNUMBER_ZONE_1');
        Configuration::deleteByName('VATNUMBER_ZONE_2');
    } catch (Exception $e) {
        $result &= false;
    }

    return (bool) $result;
}
