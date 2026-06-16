<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_2($module)
{
    $result = true;

    try {
        // $module->registerHook('actionObjectOrderAddAfter');
        // $module->registerHook('actionObjectOrderUpdateAfter');
    } catch (Exception $e) {
        $result &= false;
    }

    return (bool) $result;
}
