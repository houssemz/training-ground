<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $userInterfaceHttpDir = '../../../../UserInterface';

    $routingConfigurator->import("{$userInterfaceHttpDir}/**/*Controller.php", 'annotation')
        ->requirements(['_format' => 'json'])
    ;
};
