<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\DependencyInjection\Symfony\Compiler\MonologFingersCrossedEnvVarPass;
use App\Infrastructure\DependencyInjection\Symfony\Compiler\ResetServicesPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ResetServicesPass());

        if ($this->environment === 'prod') {
            $container->addCompilerPass(new MonologFingersCrossedEnvVarPass());
        }
    }

    private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $configDir = $this->getConfigDir();

        $container->import($configDir . '/{packages}/*.{yaml,php}');
        $container->import($configDir . '/{packages}/' . $this->environment . '/*.{yaml,php}');
        $container->import($configDir . '/{services}.php');
        $container->import($configDir . '/{services}_' . $this->environment . '.php');
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $configDir = $this->getConfigDir();

        $routes->import($configDir . '/{routes}/' . $this->environment . '/*.yaml');
        $routes->import($configDir . '/{routes}/*.yaml');
        $routes->import($configDir . '/{routes}.php');
    }
}
