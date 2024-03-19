<?php

declare(strict_types=1);

namespace App\Domain\Shared\Order;

readonly class OrderReference
{
    public function __construct(
        private string $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
