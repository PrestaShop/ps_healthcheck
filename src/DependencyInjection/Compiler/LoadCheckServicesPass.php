<?php

namespace PrestaShop\Module\HealthCheck\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * The checks runner is a class that register all the checks to make into a collection.
 * This class adds in the collection all the services that are tagged with 'admin.prestashop.healthcheck'.
 * You can define them in any service configuration file in the project.
 */
class LoadCheckServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Check if the primary service is defined
        if (!$container->has('prestashop.module.healthcheck.checks_runner')) {
            return;
        }

        $definition = $container->findDefinition('prestashop.module.healthcheck.checks_runner');

        // Find all service IDs with the prestashop.healthcheck tag
        $taggedServices = $container->findTaggedServiceIds('admin.prestashop.healthcheck');

        foreach ($taggedServices as $id => $tags) {
            // add the service to the Checks collection service
            $definition->addMethodCall('addCheck', [new Reference($id)]);
        }
    }
}
