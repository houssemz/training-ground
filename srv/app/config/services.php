<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function (ContainerConfigurator $container): void {
    $container->import('../src/Infrastructure/DependencyInjection/Symfony/config/*.php');

    $parameters = $container->parameters();
    $parameters->set('.container.dumper.inline_factories', true);
};
