<?php

declare(strict_types=1);

namespace App\Domain\Write\Order;

use App\Domain\Shared\AggregateRoot\Exception\AggregateEventNotImplemented;
use App\Domain\Shared\Order\OrderReference;
use App\Domain\Shared\Order\Place;
use App\Domain\Write\Order\Event\OrderCreated;
use App\Domain\Write\Order\Event\OrderEvent;
use Assert\Assertion;
use Symfony\Component\Uid\Uuid;

class Order
{
    private OrderReference $reference;

    private Uuid $id;

    private int $version;

    /**
     * @var array<OrderEvent>
     */
    private array $eventsToRecord;

    private function __construct(
    ) {
        $this->version = 0;
        $this->eventsToRecord = [];
    }

    public static function with(
        OrderReference $reference,
        \DateTimeImmutable $creationDate,
        Place $loading,
        Place $delivery,
    ): self {
        $newInstance = new self();
        $event = new OrderCreated(
            id: Uuid::v4(),
            aggregateId: Uuid::v4(),
            version: 1,
            reference: $reference,
            occurredOn: $creationDate,
            loading: $loading,
            delivery: $delivery
        );

        $newInstance->recordThat($event);

        return $newInstance;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function reference(): OrderReference
    {
        return $this->reference;
    }

    /**
     * @return array<OrderEvent>
     */
    public function eventsToRecord(): array
    {
        $events = $this->eventsToRecord;
        $this->eventsToRecord = [];

        return $events;
    }

    private function recordThat(OrderEvent $event): void
    {
        $this->applyEvent($event);
        $this->eventsToRecord[] = clone $event;
    }

    private function applyEvent(OrderEvent $event): void
    {
        switch ($event::class) {
            case OrderCreated::class:
                $this->id = $event->aggregateId();
                $this->reference = $event->reference();
                break;
            default:
                throw new AggregateEventNotImplemented($event::class);
        }
        $this->bumpVersion($event);
    }

    private function bumpVersion(OrderEvent $event): void
    {
        Assertion::eq(
            $this->version() + 1,
            $event->version(),
            sprintf(
                'Invalid aggregate version, can\'t bump from %d, to %d',
                $this->version(),
                $event->version()
            )
        );

        $this->version = $event->version();
    }
}
