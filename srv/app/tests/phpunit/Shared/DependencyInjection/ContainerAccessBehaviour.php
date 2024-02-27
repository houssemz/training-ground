<?php

declare(strict_types=1);

namespace App\Tests\PHPUnit\Shared\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

trait ContainerAccessBehaviour
{
    abstract protected static function getContainer(): ContainerInterface;

    /**
     * @template T of object
     *
     * @param class-string<T>|string $id
     *
     * @return object|T
     */
    protected static function get(string $id): object
    {
        $container = static::getContainer();

        try {
            return $container->get($id);
        } catch (ServiceNotFoundException $e) {
            try {
                return $container->get("test.{$id}");
            } catch (ServiceNotFoundException $e2) {
                throw $e;
            }
        }
    }
}
