<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection\Symfony\Compiler;

use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResetServicesPass implements CompilerPassInterface
{
    private const SERVICES_TO_RESET = [
        Connection::class => 'close',
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::SERVICES_TO_RESET as $id => $method) {
            $definition = $container->findDefinition($id);

            if (!$definition->hasTag('kernel.reset')) {
                $definition->addTag('kernel.reset', ['method' => $method, 'priority' => -100]);
            }
        }
    }
}
