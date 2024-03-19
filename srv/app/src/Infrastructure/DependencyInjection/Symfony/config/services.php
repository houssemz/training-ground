<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Domain\Write\Order\Store\OrderStore;
use App\Infrastructure\Messaging\Symfony\Messenger\Bus\SymfonyQueryBus;
use App\Infrastructure\Persistence\Doctrine\DBAL\Repository\Store\DoctrineDBALOrderStore;
use App\Infrastructure\Persistence\Doctrine\DBAL\Table\DoctrineDBALTableSchemaConfigurator;
use App\Infrastructure\Persistence\Doctrine\Migrations\Schema\DoctrineMigrationSchemaProvider;

return function (ContainerConfigurator $container): void {
    $srcDir = '%kernel.project_dir%/src';
    $infrastructureDir = "{$srcDir}/Infrastructure";
    $userInterfaceDir = "{$srcDir}/UserInterface";
    $domainDir = "{$srcDir}/Domain";
    $applicationDir = "{$srcDir}/Application";

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

    $services
        ->load('App\\Application\\Command\\', "{$applicationDir}/Command/**/*Handler.php")
        ->tag('messenger.message_handler', ['bus' => 'command.bus'])
    ;

    $services
        ->load('App\\Application\\Query\\', "{$applicationDir}/Query/**/*Handler.php")
        ->tag('messenger.message_handler', ['bus' => 'query.bus'])
    ;
    $services
        ->get(SymfonyQueryBus::class)
        ->arg('$messageBus', service('query.bus'))
    ;

    $services->alias(OrderStore::class, DoctrineDBALOrderStore::class);

    // controllers are imported separately at the end to make sure services can be injected
    // as action arguments even if you don't extend any base controller class.
    $services
        ->load('App\\UserInterface\\Http\\', "{$userInterfaceDir}/Http")
        ->tag('controller.service_arguments')
    ;
};
