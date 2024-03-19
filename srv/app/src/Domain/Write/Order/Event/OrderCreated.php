<?php

declare(strict_types=1);

namespace App\Domain\Write\Order\Event;

use App\Domain\Shared\Order\OrderReference;
use App\Domain\Shared\Order\Place;
use Symfony\Component\Uid\Uuid;

class OrderCreated extends OrderEvent
{
    private const EVENT_NAME = 'order_created';

    public function __construct(
        protected Uuid $id,
        protected Uuid $aggregateId,
        protected int $version,
        protected \DateTimeImmutable $occurredOn,
        private OrderReference $reference,
        private Place $loading,
        private Place $delivery,
    ) {
        parent::__construct(
            id: $id,
            aggregateId: $aggregateId,
            version: $version,
            occurredOn: $occurredOn,
        );
    }

    /**
     * @param array{
     *          id: string,
     *          aggregateId: string,
     *          version: int,
     *          occurrredOn: string,
     *          reference: string,
     *          recordedOn: string,
     *          loading: string,
     *          delivery: string
     *        } $data
     */
    public static function denormalize(mixed $data): self
    {
        $instance = new self(
            id: Uuid::fromString($data['id']),
            aggregateId: Uuid::fromString($data['id']),
            version: $data['version'],
            occurredOn: \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM, $data['occurredOn']), // @phpstan-ignore-line
            reference: new OrderReference($data['reference']),
            loading: new Place($data['loading']),
            delivery: new Place($data['delivery'])
        );

        $instance->recordedOn = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM, $data['recordedOn']);  // @phpstan-ignore-line

        return $instance;
    }

    public static function name(): string
    {
        return self::EVENT_NAME;
    }

    public function reference(): OrderReference
    {
        return $this->reference;
    }

    /**
     * @return array{
     *          id: string,
     *          aggregateId: string,
     *          version: int,
     *          occurredOn: string,
     *          reference: string,
     *          loading: string,
     *          delivery: string
     * }
     */
    public function normalize(): array
    {
        return [
            'id' => $this->id->__toString(),
            'aggregateId' => $this->aggregateId->__toString(),
            'version' => $this->version,
            'occurredOn' => $this->occurredOn->format(\DateTimeImmutable::ATOM),
            'reference' => $this->reference->__toString(),
            'recordedOn' => $this->recordedOn->format(\DateTimeImmutable::ATOM),
            'loading' => $this->loading->identifier,
            'delivery' => $this->delivery->identifier,
        ];
    }

    public function loading(): Place
    {
        return $this->loading;
    }

    public function delivery(): Place
    {
        return $this->delivery;
    }
}
