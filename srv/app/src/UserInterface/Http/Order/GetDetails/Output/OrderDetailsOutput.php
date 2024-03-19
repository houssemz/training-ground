<?php

declare(strict_types=1);

namespace App\UserInterface\Http\Order\GetDetails\Output;

use App\Domain\Read\OrderDetails\OrderDetails;

class OrderDetailsOutput
{
    private function __construct(
        public string $id,
        public string $reference,
        public \DateTimeImmutable $createdAt,
        public string $loading,
        public string $delivery,
    ) {
    }

    public static function fromDomain(OrderDetails $orderDetails): self
    {
        return new self(
            $orderDetails->id()->__toString(),
            $orderDetails->reference()->__toString(),
            $orderDetails->createdAt(),
            $orderDetails->loading()->identifier,
            $orderDetails->delivery()->identifier
        );
    }

    /**
     * @return array<string, int|string>
     */
    public function normalize(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'createdAt' => $this->createdAt->format(\DateTimeImmutable::ATOM),
            'loading' => $this->loading,
            'delivery' => $this->delivery,
        ];
    }
}
