<?php

declare(strict_types=1);

namespace App\Tests\PHPUnit\Shared\TestCase;

use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessagingTestCase extends KernelTestCase
{
    use InteractsWithMessenger;
}
