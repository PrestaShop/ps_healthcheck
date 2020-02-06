<?php

namespace PrestaShop\Module\HealthCheck;

use PrestaShop\Module\HealthCheck\DependencyInjection\Compiler\LoadCheckServicesPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PsHealthcheckBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new LoadCheckServicesPass('admin'), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
    }
}
