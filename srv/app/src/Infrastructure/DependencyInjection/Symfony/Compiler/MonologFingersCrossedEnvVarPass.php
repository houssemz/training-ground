<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection\Symfony\Compiler;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * With default monolog configuration, the action_level env var is evaluated at build time not at runtime.
 * This compiler pass override this behavior.
 */
class MonologFingersCrossedEnvVarPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition('monolog.handler.main');
        $class = $definition->getClass();

        if ($class !== FingersCrossedHandler::class) {
            return;
        }

        $activationStrategyArgument = $definition->getArgument(1);

        if ($activationStrategyArgument instanceof Reference) {
            $errorLevelActivationStrategyDefinition = $this->findErrorLevelActivationStrategyDefinition($container, $activationStrategyArgument);
            $errorLevelActivationStrategyDefinition?->replaceArgument(0, '%env(MONOLOG_FINGERS_CROSSED_ACTION_LEVEL)%');

            return;
        }

        $definition->replaceArgument(1, '%env(MONOLOG_FINGERS_CROSSED_ACTION_LEVEL)%');
    }

    private function findErrorLevelActivationStrategyDefinition(ContainerBuilder $containerBuilder, Reference $reference): ?Definition
    {
        $definition = $containerBuilder->findDefinition($reference->__toString());

        if ($reference->__toString() === ErrorLevelActivationStrategy::class || $definition->getClass() === ErrorLevelActivationStrategy::class) {
            return $definition;
        }

        foreach ($definition->getArguments() as $argument) {
            if ($argument instanceof Definition && $argument->getClass() === ErrorLevelActivationStrategy::class) {
                return $argument;
            }

            if (!$argument instanceof Reference) {
                continue;
            }

            if (null !== $argumentDefinition = $this->findErrorLevelActivationStrategyDefinition($containerBuilder, $argument)) {
                return $argumentDefinition;
            }
        }

        return null;
    }
}
