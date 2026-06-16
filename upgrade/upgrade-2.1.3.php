<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_3($module)
{
    $result = true;

    try {
        $module->registerHook('actionValidateOrder');
        $module->unregisterHook('actionValidateCustomerAddressForm');
    } catch (Exception $e) {
        $result &= false;
    }

    return (bool) $result;
}
