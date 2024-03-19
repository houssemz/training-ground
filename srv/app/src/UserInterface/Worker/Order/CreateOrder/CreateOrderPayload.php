<?php

declare(strict_types=1);

namespace App\UserInterface\Worker\Order\CreateOrder;

readonly class CreateOrderPayload
{
    public function __construct(
        public string $reference,
        public string $loadingIdentifier,
        public string $deliveryIdentifier,
        public \DateTimeImmutable $creationDate,
    ) {
    }
}
