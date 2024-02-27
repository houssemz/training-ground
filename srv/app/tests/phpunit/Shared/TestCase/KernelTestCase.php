<?php

declare(strict_types=1);

namespace App\Tests\PHPUnit\Shared\TestCase;

use App\Tests\PHPUnit\Shared\DependencyInjection\ContainerAccessBehaviour;
use App\Tests\PHPUnit\Shared\Persistence\TransactionalBehavior;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as SymfonyKernelTestCase;

class KernelTestCase extends SymfonyKernelTestCase
{
    use ContainerAccessBehaviour;
    use TransactionalBehavior;
}
