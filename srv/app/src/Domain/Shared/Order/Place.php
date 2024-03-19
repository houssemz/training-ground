<?php

declare(strict_types=1);

namespace App\Domain\Shared\Order;

readonly class Place
{
    public function __construct(
        public string $identifier
    ) {
    }
}
