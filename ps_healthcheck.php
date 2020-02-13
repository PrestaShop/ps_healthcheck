<?php
/**
 * 2007-2020 PrestaShop SA and Contributors.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_CAN_LOAD_FILES_')) {
    exit(1);
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use PrestaShop\Module\HealthCheck\Repository\HealthCheckConfigRepository;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

/**
 * Class Ps_Healthcheck.
 */
class Ps_Healthcheck extends Module
{
    const MODULE_NAME = 'ps_healthcheck';
    /**
     * @var HealthCheckConfigRepository
     */
    private $repository;

    public function __construct()
    {
        $this->name = static::MODULE_NAME;
        $this->author = 'PrestaShop';
        $this->version = '0.1.0';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->description = $this->trans('Adds an endpoint to perform application health check.', [], 'Modules.Healthcheck.Admin');
        $this->secure_key = Tools::encrypt($this->name);
        $this->ps_versions_compliancy = ['min' => '1.7.7.0', 'max' => _PS_VERSION_];

        parent::__construct();
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $installed = false;
        if (null !== $this->getRepository()) {
            $installed = $this->installFixtures();
        }

        if (!$installed) {
            $this->uninstall();
        }

        return $installed;
    }

    /**
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function installFixtures()
    {
        $installed = true;
        $errors = $this->getRepository()->createTables();
        if (!empty($errors)) {
            $this->addModuleErrors($errors);
            $installed = false;
        }

        $errors = $this->getRepository()->installFixtures();
        if (!empty($errors)) {
            $this->addModuleErrors($errors);
            $installed = false;
        }

        return $installed;
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminHealthCheck')
        );
    }

    private function addModuleErrors(array $errors)
    {
        foreach ($errors as $error) {
            $this->_errors[] = $this->trans($error['key'], $error['parameters'], $error['domain']);
        }
    }

    /**
     * @return HealthCheckConfigRepository|null
     */
    private function getRepository()
    {
        if (null === $this->repository && $this->isSymfonyContext()) {
            try {
                $this->repository = $this->get('prestashop.module.healthcheck.repository');
            } catch (\Exception $e) {
                //Module is not installed so its services are not loaded
                $this->repository = new HealthCheckConfigRepository(
                    $this->get('doctrine.dbal.default_connection'),
                    SymfonyContainer::getInstance()->getParameter('database_prefix')
                );
            }
        }

        return $this->repository;
    }
}
