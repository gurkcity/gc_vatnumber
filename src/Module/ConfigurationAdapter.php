<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

namespace Onlineshopmodule\PrestaShop\Module\Vatnumber\Module;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class ConfigurationAdapter
{
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var ShopConstraint|null
     */
    protected $shopConstraint;

    /**
     * @var array
     */
    protected $languages = [];

    /**
     * @var \Context
     */
    protected $context;

    /**
     * @var int
     */
    protected $idLang;

    public function __construct(
        \GC_Vatnumber $module,
        Configuration $configuration,
        array $languages,
        \Context $context
    ) {
        $this->prefix = strtoupper($module->name) . '_';
        $this->configuration = $configuration;
        $this->shopConstraint = $this->initShopConstraintFromShopContext();
        $this->languages = $languages;
        $this->context = $context;
        $this->idLang = (int) ($context->language->id ?? 0);
    }

    public function get(
        string $key,
        $default = null,
        ?ShopConstraint $shopConstraint = null,
        bool $withPrefix = true
    ) {
        return $this->configuration->get(
            ($withPrefix ? $this->prefix : '') . $key,
            $default,
            $shopConstraint ?: $this->shopConstraint ?: null
        );
    }

    public function getLang(
        string $key,
        $default = null,
        ?ShopConstraint $shopConstraint = null,
        bool $withPrefix = true
    ): array {
        $values = $this->configuration->get(
            ($withPrefix ? $this->prefix : '') . $key,
            $default,
            $shopConstraint ?: $this->shopConstraint ?: null
        );

        $configuration = [];

        foreach ($this->languages as $lang) {
            $idLang = (int) $lang['id_lang'];

            if (!isset($values[$idLang])) {
                $configuration[$idLang] = '';
            } else {
                $configuration[$idLang] = $values[$idLang];
            }
        }

        return $configuration;
    }

    public function getLangValue(
        string $key,
        int $idLang = 0,
        $default = null,
        ?ShopConstraint $shopConstraint = null,
        bool $withPrefix = true
    ): string {
        $values = $this->getLang($key, $default, $shopConstraint, $withPrefix);

        if ($idLang && isset($values[$idLang])) {
            return $values[$idLang];
        }

        $idLang = $this->idLang;

        if ($idLang && isset($values[$idLang])) {
            return $values[$idLang];
        }

        $idLang = (int) $this->configuration->get('PS_LANG_DEFAULT');

        if ($idLang && isset($values[$idLang])) {
            return $values[$idLang];
        }

        return $default;
    }

    public function set(
        string $key,
        $values,
        bool $html = false,
        ?ShopConstraint $shopConstraint = null,
        bool $withPrefix = true
    ): bool {
        $options['html'] = $html;

        $this->configuration->set(
            ($withPrefix ? $this->prefix : '') . $key,
            $values,
            $shopConstraint ?: $this->shopConstraint ?: null,
            $options
        );

        return true;
    }

    public function setLang(
        string $key,
        $values,
        bool $html = false,
        ?ShopConstraint $shopConstraint = null,
        bool $withPrefix = true
    ): bool {
        $options['html'] = $html;

        $configuration = [];

        foreach ($this->languages as $lang) {
            $idLang = (int) $lang['id_lang'];

            if (!isset($values[$idLang])) {
                $configuration[$idLang] = '';
            } else {
                $configuration[$idLang] = $values[$idLang];
            }
        }

        $this->configuration->set(
            ($withPrefix ? $this->prefix : '') . $key,
            $configuration,
            $shopConstraint ?: $this->shopConstraint ?: null,
            $options
        );

        return true;
    }

    public function delete(string $key, bool $withPrefix = true): bool
    {
        $this->configuration->remove(($withPrefix ? $this->prefix : '') . $key);

        return true;
    }

    public function deleteFromContext(
        string $key,
        ?ShopConstraint $shopConstraint = null,
        bool $withPrefix = true
    ): bool {
        $this->configuration->deleteFromContext(
            ($withPrefix ? $this->prefix : '') . $key,
            $shopConstraint ?: $this->shopConstraint ?: null
        );

        return true;
    }

    public function getGlobal(string $key, bool $withPrefix = true)
    {
        $shopConstraint = ShopConstraint::allShops();

        return $this->get($key, null, $shopConstraint, $withPrefix);
    }

    public function setGlobal(
        string $key,
        $values,
        $html = false,
        bool $withPrefix = true
    ): bool {
        $shopConstraint = ShopConstraint::allShops();

        $this->set(
            $key,
            $values,
            $html,
            $shopConstraint,
            $withPrefix
        );

        return true;
    }

    public function has(
        string $key,
        ?ShopConstraint $shopConstraint = null,
        bool $withPrefix = true
    ): bool {
        return $this->configuration->has(
            ($withPrefix ? $this->prefix : '') . $key,
            $shopConstraint ?: $this->shopConstraint ?: null
        );
    }

    public function hasLang(
        string $key,
        ?ShopConstraint $shopConstraint = null,
        bool $withPrefix = true
    ): bool {
        return $this->configuration->has(
            ($withPrefix ? $this->prefix : '') . $key,
            $shopConstraint ?: $this->shopConstraint ?: null
        );
    }

    public function getName(string $key, bool $withPrefix = true): string
    {
        return ($withPrefix ? $this->prefix : '') . $key;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    private function initShopConstraintFromShopContext(): ShopConstraint
    {
        if (\Shop::getContext() === \Shop::CONTEXT_SHOP) {
            return ShopConstraint::shop(\Shop::getContextShopID());
        } elseif (\Shop::getContext() === \Shop::CONTEXT_GROUP) {
            return ShopConstraint::shopGroup(\Shop::getContextShopGroupID());
        }

        return ShopConstraint::allShops();
    }
}
