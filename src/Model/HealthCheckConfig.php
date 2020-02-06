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

namespace PrestaShop\Module\HealthCheck\Model;

/**
 * Class HealthCheckConfig.
 */
class HealthCheckConfig extends \ObjectModel
{
    /**
     * @var int
     */
    public $id_health_check_config;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $ips;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'health_check_config',
        'primary' => 'id_health_check_config',
        'multilang' => false,
        'fields' => [
            'token' => ['type' => self::TYPE_STRING, 'lang' => false, 'required' => true, 'size' => 255],
            'ips' => ['type' => self::TYPE_STRING, 'lang' => false, 'required' => true, 'size' => 255],
        ],
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
            'ips' => $this->ips,
        ];
    }
}
