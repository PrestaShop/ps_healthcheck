<?php
/**
 * 2007-2020 PrestaShop SA and Contributors.
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors.
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\HealthCheck\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HealthCheckController.
 *
 * @ModuleActivated(moduleName="ps_healthcheck", redirectRoute="admin_module_manage")
 */
class HealthCheckController extends FrameworkBundleAdminController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $runner = $this->get('prestashop.module.healthcheck.checks_runner');
        $checkResults = $runner->run();

        $status = ($checkResults->getFailureCount() + $checkResults->getWarningCount()) > 0 ? false : true;

        return $this->render('@Modules/ps_healthcheck/views/templates/admin/index.html.twig', [
            'messages' => $runner->getMessages(),
            'status' => $status,
        ], new Response('', $status ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST));
    }

    public function configureAction(Request $request)
    {
        $form = $this->get('prestashop.module.healthcheck.form_handler')->getForm();

        return $this->render('@Modules/ps_healthcheck/views/templates/admin/health_check/form.html.twig', [
            'healthCheckConfigForm' => $form->createView(),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }

    public function configureProcessAction(Request $request)
    {
        $formHandler = $this->get('prestashop.module.healthcheck.form_handler');
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $saveErrors = $formHandler->save($data);

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_healthcheck_configure');
            }

            $this->flashErrors($saveErrors);
        }

        return $this->render('@Modules/ps_healthcheck/views/templates/admin/health_check/form.html.twig', [
            'healthCheckConfigForm' => $form->createView(),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
        ]);
    }
}
