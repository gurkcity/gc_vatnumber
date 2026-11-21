<?php

/**
 * GC Vatnumber
 * Module for PrestaShop E-Commerce Software
 *
 * @author    Markus Engel <info@onlineshop-module.de>
 * @copyright Copyright (c) 2026, Onlineshop-Module.de
 * @license   commercial, see licence.txt
 */

namespace Onlineshopmodule\PrestaShop\Module\Vatnumber\Controller;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ModuleActivated(moduleName="gc_vatnumber", redirectRoute="gc_vatnumber_license")
 */
class ConfigurationAdminController extends AdminController
{
    /**
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller')) && is_granted('update', request.get('_legacy_controller')) && is_granted('create', request.get('_legacy_controller')) && is_granted('delete', request.get('_legacy_controller'))",
     *     message="Access denied."
     * )
     */
    public function indexAction(
        Request $request,
        #[Autowire(service: 'onlineshopmodule.module.vatnumber.form.handler.configuration')]
        FormHandlerInterface $configurationFormHandler
    ): Response {
        $this->setLayoutTitle($this->trans('Configuration', [], 'Modules.Gcvatnumber.Admin'));

        if ($this->module->redirectAdminConfigurationPermanentTo) {
            return $this->redirectToRoute($this->module->redirectAdminConfigurationPermanentTo);
        }

        return $this->processForm(
            $request,
            $configurationFormHandler,
            'gc_vatnumber_configuration',
            'views/templates/admin/configuration.html.twig'
        );
    }
}
