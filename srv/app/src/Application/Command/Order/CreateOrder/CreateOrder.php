<?php

declare(strict_types=1);

namespace App\Application\Command\Order\CreateOrder;

use App\Application\Command\Command;

readonly class CreateOrder implements Command
{
    public function __construct(
        public string $orderReference,
        public string $loadingIdentifier,
        public string $deliveryIdentifier,
        public \DateTimeImmutable $creationDate,
    ) {
    }
}
