<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Infrastructure\Persistence\Doctrine\DBAL\Table\DoctrineDBALTableSchemaConfigurator;
use App\Infrastructure\Persistence\Doctrine\Migrations\Schema\DoctrineMigrationSchemaProvider;

return function (ContainerConfigurator $container): void {
    $srcDir = '%kernel.project_dir%/src';
    $infrastructureDir = "{$srcDir}/Infrastructure";
    //    $userInterfaceDir = "$srcDir/UserInterface";
    $domainDir = "{$srcDir}/Domain";

    $excluded = [
        "{$infrastructureDir}/Persistence/Doctrine/Migrations/migrations/*.php",
        "{$infrastructureDir}/DependencyInjection/Symfony/config/*.php",
        "{$infrastructureDir}/Routing/Symfony/config/*.php",
        $domainDir,
    ];

    $services = $container->services();
    $services->defaults()
        ->private()
        ->autoconfigure()
        ->autowire()
    ;

    $services
        ->instanceof(DoctrineDBALTableSchemaConfigurator::class)
        ->tag(DoctrineMigrationSchemaProvider::class)
    ;

    $services->load('App\\', $srcDir)
        ->exclude($excluded)
    ;

    $services->set(DoctrineMigrationSchemaProvider::class)
        ->arg('$tableSchemaConfigurators', tagged_iterator(DoctrineMigrationSchemaProvider::class))
    ;

    // controllers are imported separately at the end to make sure services can be injected
    // as action arguments even if you don't extend any base controller class.
    //    $services
    //        ->load('App\\UserInterface\\Http\\', "$userInterfaceDir/Http")
    //        ->tag('controller.service_arguments');
};
