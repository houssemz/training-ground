<?php

declare(strict_types=1);

namespace App\Domain\Read\OrderDetails;

use App\Domain\Shared\AggregateRoot\Exception\AggregateEventNotImplemented;
use App\Domain\Shared\Order\OrderReference;
use App\Domain\Shared\Order\Place;
use App\Domain\Write\Order\Event\OrderCreated;
use App\Domain\Write\Order\Event\OrderEvent;
use Symfony\Component\Uid\Uuid;

class OrderDetails
{
    private Uuid $id;

    private \DateTimeImmutable $createdAt;

    private OrderReference $reference;

    private Place $loading;

    private Place $delivery;

    /**
     * @param \Traversable<OrderEvent> $events
     */
    public function __construct(
        \Traversable $events
    ) {
        foreach ($events as $event) {
            $this->applyEvent($event);
        }
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function reference(): OrderReference
    {
        return $this->reference;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function loading(): Place
    {
        return $this->loading;
    }

    public function delivery(): Place
    {
        return $this->delivery;
    }

    private function applyEvent(OrderEvent $event): void
    {
        switch (true) {
            case $event instanceof OrderCreated:
                $this->id = $event->id();
                $this->reference = $event->reference();
                $this->createdAt = $event->occurredOn();
                $this->loading = $event->loading();
                $this->delivery = $event->delivery();
                break;
            default:
                throw new AggregateEventNotImplemented($event::class);
        }
    }
}
