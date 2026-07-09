<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\Decimal\Number;
use PrestaShop\Decimal\Operation\Rounding;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__.'/vendor/autoload.php';

class VatNumber extends TaxManagerModule
{
    public function __construct()
    {
        $this->name = 'vatnumber';
        $this->tab = 'billing_invoicing';
        $this->version = '2.1.5';
        $this->author = 'Onlineshop-Module';
        $this->need_instance = 0;

        $this->tax_manager_class = 'VATNumberTaxManager';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('European VAT number');
        $this->description = $this->l('Enables you to enter the intra-community VAT number when creating the address. You must fill in the company field to allow entering the VAT number.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return
            parent::install()
            && Configuration::updateValue('VATNUMBER_MANAGEMENT', 0)
            && $this->registerHook('actionValidateCustomerAddressForm')
            && $this->registerHook('actionValidateOrder')
            && $this->registerHook('actionFrontControllerSetVariables');
    }

    public function enable($force_all = false)
    {
        parent::enable($force_all);
        Configuration::updateValue('VATNUMBER_MANAGEMENT', 0);

        return true;
    }

    public static function getPrefixIntracomVAT()
    {
        $intracom_array = array(
            'AT' => 'AT',
            //Austria
            'BE' => 'BE',
            //Belgium
            'DK' => 'DK',
            //Denmark
            'FI' => 'FI',
            //Finland
            'FR' => 'FR',
            //France
            'FX' => 'FR',
            //France métropolitaine
            'DE' => 'DE',
            //Germany
            'GR' => 'EL',
            //Greece
            'IE' => 'IE',
            //Irland
            'IT' => 'IT',
            //Italy
            'LU' => 'LU',
            //Luxembourg
            'NL' => 'NL',
            //Netherlands
            'PT' => 'PT',
            //Portugal
            'ES' => 'ES',
            //Spain
            'SE' => 'SE',
            //Sweden
            'GB' => 'GB',
            //United Kingdom
            'CY' => 'CY',
            //Cyprus
            'EE' => 'EE',
            //Estonia
            'HU' => 'HU',
            //Hungary
            'LV' => 'LV',
            //Latvia
            'LT' => 'LT',
            //Lithuania
            'MT' => 'MT',
            //Malta
            'PL' => 'PL',
            //Poland
            'SK' => 'SK',
            //Slovakia
            'CZ' => 'CZ',
            //Czech Republic
            'SI' => 'SI',
            //Slovenia
            'RO' => 'RO',
            //Romania
            'BG' => 'BG',
            //Bulgaria
            'HR' => 'HR',
            //Croatia
        );

        return $intracom_array;
    }

    public static function isApplicable($id_country)
    {
        return (((int)$id_country && array_key_exists(Country::getIsoById($id_country), self::getPrefixIntracomVAT())) ? 1 : 0);
    }

    public static function WebServiceCheck($vat_number)
    {
        if (empty($vat_number))
            return array();
        $vat_number = str_replace(' ', '', $vat_number);
        $prefix = Tools::substr($vat_number, 0, 2);
        if (array_search($prefix, self::getPrefixIntracomVAT()) === false)
            return array(Tools::displayError('Invalid VAT number'));
        $vat = Tools::substr($vat_number, 2);
        $url = 'http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms='.urlencode($prefix).'&iso='.urlencode($prefix).'&vat='.urlencode($vat);
        @ini_set('default_socket_timeout', 2);
        for ($i = 0; $i < 2; $i++)
        {
            if ($page_res = Tools::file_get_contents($url, false, null, 10))
            {
                if (preg_match('/invalid VAT number/i', $page_res))
                {
                    @ini_restore('default_socket_timeout');

                    return array(Tools::displayError('VAT number not found'));
                }
                else if (preg_match('/valid VAT number/i', $page_res))
                {
                    @ini_restore('default_socket_timeout');

                    return array();
                }
                else
                    ++$i;
            }
            else
                sleep(1);
        }
        @ini_restore('default_socket_timeout');

        return array(Tools::displayError('VAT number validation service unavailable'));
    }

    public function getContent()
    {
        $echo = '';
        if (Tools::isSubmit('submitVatNumber')) {
            $home_country = Tools::getValue('VATNUMBER_HOME_COUNTRY', []);
            if (!is_array($home_country)) {
                $home_country = [];
            }

            $eu_countries = Tools::getValue('vatnumber-eu-countries-listBox', []);
            if (!is_array($eu_countries)) {
                $eu_countries = [];
            }

            $result = true;
            $result &= Configuration::updateValue('VATNUMBER_HOME_COUNTRY', json_encode(array_map('intval', $home_country)));
            $result &= Configuration::updateValue('VATNUMBER_EU_COUNTRIES', json_encode(array_map('intval', $eu_countries)));

            if ($result) {
                $echo .= $this->displayConfirmation($this->l('The settings has been updated successfully'));
            }
        }
        $echo .= $this->renderForm();

        return $echo;
    }

    public function renderForm()
    {
        $countries = array_merge(
            [
                [
                    'id_country' => 0,
                    'country' => $this->l('-- Choose a country --'),
                ]
            ],
            array_map(
                function ($item) {
                    return [
                        'id_country' => $item['id_country'],
                        'country' => $item['country'] . ' (' . $item['id_country'] . ')',
                    ];
                },
                Country::getCountries($this->context->language->id, false, false, false)
            )
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Home country'),
                        'desc' => $this->l('Select a country here that corresponds to you home country'),
                        'name' => 'VATNUMBER_HOME_COUNTRY',
                        'multiple' => true,
                        'required' => true,
                        // 'default_value' => null,
                        'options' => array(
                            'query' => $countries,
                            'id' => 'id_country',
                            'name' => 'country',
                        ),
                        'size' => 10, // display 10 items in frame
                    ),
                    array(
                        'type' => 'html',
                        'html_content' => $this->renderCountriesList(),
                        'name' => 'eu_countries',
                        'col' => 12,
                        'label' => false,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitVatNumber',
                )
            ),
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = '';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'VATNUMBER_HOME_COUNTRY[]' => Tools::getValue('VATNUMBER_HOME_COUNTRY', json_decode((string) Configuration::get('VATNUMBER_HOME_COUNTRY'), true)),
        );
    }

    public function hookActionValidateOrder($params)
    {
        if (
            empty($params['order'])
            || !($params['order'] instanceof Order)
        ) {
            return;
        }

        $this->updateOrder($params['order']);
    }

    public function updateOrder(Order $order)
    {
        $idAddress = $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')};

        $address = new Address($idAddress);

        if (VATNumberTaxManager::isAvailableForThisAddress($address)) {
            $this->copyTaxInclToTaxExclInOrder($order);
        } else {
            $this->correctTaxExclValuesToPrecision($order, 2);
        }
    }

    public function copyTaxInclToTaxExclInOrder(Order $order)
    {
        DB::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'order_detail`
            SET
                `unit_price_tax_excl` = `unit_price_tax_incl`,
                `total_price_tax_excl` = `total_price_tax_incl`
            WHERE
                `id_order` = ' . (int) $order->id . '
        ');

        DB::getInstance()->execute('
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

        $order->total_shipping_tax_incl = (new Number((string)$order->total_shipping_tax_incl))->toPrecision(2, Rounding::ROUND_HALF_EVEN);

        $order->total_products = $order->total_products_wt;
        $order->total_shipping_tax_excl = $order->total_shipping_tax_incl;
        $order->total_shipping = $order->total_shipping_tax_incl;
        $order->total_paid_tax_excl = $order->total_paid_tax_incl;
        $order->total_paid = $order->total_paid_tax_incl;
    }

    public function correctTaxExclValuesToPrecision(Order $order, int $precision = 6)
    {
        $order->total_shipping_tax_incl = (new Number((string)$order->total_shipping_tax_incl))->toPrecision($precision, Rounding::ROUND_HALF_EVEN);
        $order->total_shipping = $order->total_shipping_tax_incl;
    }

    private function renderCountriesList()
    {
        $zones_array = array();
        $this->zones = Zone::getZones();
        foreach ($this->zones as $zone) {
            $zones_array[$zone['id_zone']] = $zone['name'];
        }

        $fields_list = array(
            'id_country' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'type' => 'text',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Country', array(), 'Admin.Global'),
                'type' => 'text',
                'filter_key' => 'b!name',
            ),
            'iso_code' => array(
                'title' => $this->trans('ISO code', array(), 'Admin.International.Feature'),
                'type' => 'text',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'call_prefix' => array(
                'title' => $this->trans('Call prefix', array(), 'Admin.International.Feature'),
                'type' => 'text',
                'align' => 'center',
                'callback' => 'displayCallPrefix',
                'callback_object' => $this,
                'class' => 'fixed-width-sm',
            ),
            'zone' => array(
                'title' => $this->trans('Zone', array(), 'Admin.Global'),
                'type' => 'select',
                'list' => $zones_array,
                'filter_key' => 'z!id_zone',
                'filter_type' => 'int',
                'order_key' => 'z!name',
            ),
            'active' => array(
                'title' => $this->trans('Enabled', array(), 'Admin.Global'),
                'align' => 'center',
                // 'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active',
                'class' => 'fixed-width-sm',
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = [];
        $helper->show_toolbar = true;
        $helper->toolbar_btn = [];
        $helper->module = $this;
        $helper->identifier = 'id_country';
        $helper->title = $this->l('Select EU-Countries');
        $helper->table = 'country';
        $helper->table_id = 'vatnumber-eu-countries-list';
        $helper->list_id = $helper->table_id;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->no_link = true;
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->force_show_bulk_actions = true;
        $helper->bulk_actions = [
            'empty' => [
                'text' => 'divider'
            ],
        ];

        $helper->tpl_vars = [
            'disable_list_form' => true,
        ];

        $page = ($page = Tools::getValue('submitFilter' . $helper->list_id)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue($helper->list_id . '_pagination')) ? $pagination : 50;

        $filters = $this->handleListFilters($helper->list_id, $fields_list);

        $helper->orderBy = $this->context->cookie->{$helper->list_id . 'Orderby'};
        if (!Validate::isOrderBy($helper->orderBy) || !isset($fields_list[$helper->orderBy])) {
            $helper->orderBy = 'zone';
        }
        $helper->orderWay = strtoupper((string) $this->context->cookie->{$helper->list_id . 'Orderway'});
        if (!$helper->orderWay || !Validate::isOrderWay($helper->orderWay)) {
            $helper->orderWay = 'ASC';
        }

        $countries = $this->filterCountries($filters, (int) $page, (int) $pagination, $helper->orderBy, $helper->orderWay);
        $helper->listTotal = $this->filterCountries($filters, null, null, null, null, true);

        /* bypass the helper list logic and predefined checked items */
        $eu_countries = json_decode((string) Configuration::get('VATNUMBER_EU_COUNTRIES'), true);
        if ($eu_countries) {
            $_POST[$helper->list_id . 'Box'] = $eu_countries;
        }
        /* end bypass the helper list logic and predefined checked items */

        return $helper->generateList($countries, $fields_list);
    }

    private function filterCountries(array $params, $p = null, $limit = null, $orderBy = null, $orderWay = null, $getTotalRows = false)
    {
        $sql = 'SELECT
            SQL_CALC_FOUND_ROWS
            c.`id_country`,
            cl.`name`,
            c.`iso_code`,
            c.`call_prefix`,
            z.`id_zone`,
            c.`active`,
            z.`name` AS zone
        FROM `' . _DB_PREFIX_ . 'country` c
        LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (cl.`id_country` = c.`id_country` AND cl.`id_lang` = ' . (int) $this->context->language->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'zone` z ON (z.`id_zone` = c.`id_zone`)
        WHERE 1'
            . ((isset($params['id_country']) && trim($params['id_country']) !== '') ? ' AND c.id_country = ' . (int) $params['id_country'] : '')
            . ((isset($params['name']) && trim($params['name']) !== '') ? ' AND cl.name LIKE \'%' . pSql($params['name']) . '%\'' : '')
            . ((isset($params['iso_code']) && trim($params['iso_code']) !== '') ? ' AND c.iso_code LIKE \'%' . pSql($params['iso_code']) . '%\'' : '')
            . ((isset($params['call_prefix']) && trim($params['call_prefix']) !== '') ? ' AND c.call_prefix LIKE \'%' . pSql($params['call_prefix']) . '%\'' : '')
            . ((isset($params['zone']) && trim($params['zone']) !== '') ? ' AND c.id_zone = ' . (int) $params['zone'] : '')
            . ((isset($params['active']) && trim($params['active']) !== '') ? ' AND c.active = ' . (int) $params['active'] : '')
        ;

        if ($orderBy) {
            $sql .= ' ORDER BY ' . pSql($orderBy) . ' ' . (Validate::isOrderWay($orderWay) ? $orderWay : ' ASC');
        }

        if ($p && $limit) {
            $offset = ($p - 1) * $limit;
            $sql .= ' LIMIT ' . (int) $offset . ',' . (int) $limit;
        }

        if ($getTotalRows) {
            return (int) Db::getInstance()->getValue('SELECT FOUND_ROWS() AS `' . _DB_PREFIX_ . 'country`', false);
        }

        return Db::getInstance()->executeS($sql);
    }

    private function handleListFilters($list_id, array $fields_list = array())
    {
        if (Tools::isSubmit('submitFilter') && Tools::isSubmit('submitFilter' . $list_id)) {
            foreach ($fields_list as $field => $options) {
                $key = isset($options['filter_key']) ? $options['filter_key'] : $field;
                $value = Tools::getValue($list_id . 'Filter_' . $key);
                $this->context->cookie->{$list_id . 'Filter_' . $key} = !is_array($value) ? $value : json_encode($value);
            }
        } elseif (Tools::isSubmit('submitReset' . $list_id)) {
            foreach ($fields_list as $field => $options) {
                $key = isset($options['filter_key']) ? $options['filter_key'] : $field;
                $this->context->cookie->__unset($list_id . 'Filter_' . $key);
            }
            if (isset($this->context->cookie->{$list_id . 'Orderby'})) {
                unset($this->context->cookie->{$list_id . 'Orderby'});
            }
            if (isset($this->context->cookie->{$list_id . 'Orderway'})) {
                unset($this->context->cookie->{$list_id . 'Orderway'});
            }
            $_POST = array();
        } elseif (Tools::getValue($list_id . 'Orderby')
            || Tools::getValue($list_id . 'Orderway')
        ) {
            $orderBy = Tools::getValue($list_id . 'Orderby');
            $orderWay = Tools::getValue($list_id . 'Orderway');
            if (Validate::isOrderBy($orderBy) && isset($fields_list[$orderBy])) {
                $this->context->cookie->{$list_id . 'Orderby'} = $orderBy;
            }
            if (Validate::isOrderWay($orderWay)) {
                $this->context->cookie->{$list_id . 'Orderway'} = $orderWay;
            }
        }

        $result = array();
        foreach ($fields_list as $field => $options) {
            $key = isset($options['filter_key']) ? $options['filter_key'] : $field;
            $value = $this->context->cookie->__get($list_id . 'Filter_' . $key);
            if ($options['type'] == 'date') {
                $value = json_decode($value, true);
                $value = $value ? array('from' => $value[0], 'to' => $value[1]) : false;
            }

            $result[$field] = $value;
        }

        return $result;
    }

    /**
     * Display call prefix for countries list
     *
     * @param array $value
     *
     * @return string
     */
    public function displayCallPrefix($prefix)
    {
        return (int) $prefix ? '+' . $prefix : '-';
    }

    public function hookActionFrontControllerSetVariables($params)
    {
        try {
            $idAddress = (int) Context::getContext()->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
            $address = new Address($idAddress);

            $isB2b = false;

            if ($address instanceof Address && Validate::isLoadedObject($address)) {
                require_once(__DIR__ . '/VATNumberTaxManager.php');

                $isB2b = VATNumberTaxManager::isAvailableForThisAddress($address);
            }
        } catch (Exception $e) {
            return [];
        }

        return [
            'is_b2b' => $isB2b,
        ];
    }
}
