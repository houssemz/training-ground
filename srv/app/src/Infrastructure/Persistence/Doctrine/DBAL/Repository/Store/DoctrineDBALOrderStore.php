<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\DBAL\Repository\Store;

use App\Domain\Shared\AggregateRoot\Exception\AggregateEventNotImplemented;
use App\Domain\Write\Order\Event\OrderCreated;
use App\Domain\Write\Order\Order;
use App\Domain\Write\Order\Store\OrderStore;
use App\Infrastructure\Persistence\Doctrine\DBAL\Table\Write\OrderEventTable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;

class DoctrineDBALOrderStore implements OrderStore
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function has(Uuid $orderId): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from(OrderEventTable::TABLE_NAME)
            ->andWhere(sprintf('%s = :orderId', OrderEventTable::COLUMN_AGGREGATE_ROOT_ID))
            ->setParameter('orderId', $orderId->__toString(), 'uuid')
            ->executeQuery()
            ->fetchOne() !== 0
        ;
    }

    public function load(Uuid $orderId): \Traversable
    {
        $qb = $this->connection->createQueryBuilder()
                ->select(OrderEventTable::COLUMN_EVENT_NAME, OrderEventTable::COLUMN_DATA_SOURCE)
                ->from(OrderEventTable::TABLE_NAME)
                ->andWhere(sprintf('%s = :orderId', OrderEventTable::COLUMN_AGGREGATE_ROOT_ID))
                ->setParameter('orderId', $orderId->__toString())
        ;

        /** @var array<string, string> */
        foreach ($qb->executeQuery()->iterateAssociative() as $data) {
            $decodedEvent = json_decode($data[OrderEventTable::COLUMN_DATA_SOURCE], true, 512, \JSON_THROW_ON_ERROR);
            yield match ($data[OrderEventTable::COLUMN_EVENT_NAME]) {
                OrderCreated::name() => OrderCreated::denormalize($decodedEvent), // @phpstan-ignore-line
                default => throw new AggregateEventNotImplemented($data[OrderEventTable::COLUMN_EVENT_NAME])
            };
        }
    }

    public function save(Order $order): void
    {
        foreach ($order->eventsToRecord() as $event) {
            $this->connection->insert(
                OrderEventTable::TABLE_NAME,
                [
                    OrderEventTable::COLUMN_EVENT_ID => $event->id(),
                    OrderEventTable::COLUMN_AGGREGATE_ROOT_ID => $event->aggregateId(),
                    OrderEventTable::COLUMN_AGGREGATE_ROOT_VERSION => $event->version(),
                    OrderEventTable::COLUMN_EVENT_NAME => $event->name(),
                    OrderEventTable::COLUMN_OCCURRED_ON => $event->occurredOn(),
                    OrderEventTable::COLUMN_RECORDED_ON => $event->recordedOn(),
                    OrderEventTable::COLUMN_DATA_SOURCE => $event->normalize(),
                ],
                [
                    OrderEventTable::COLUMN_EVENT_ID => 'uuid',
                    OrderEventTable::COLUMN_AGGREGATE_ROOT_ID => 'uuid',
                    OrderEventTable::COLUMN_OCCURRED_ON => Types::DATETIMETZ_IMMUTABLE,
                    OrderEventTable::COLUMN_RECORDED_ON => Types::DATETIMETZ_IMMUTABLE,
                    OrderEventTable::COLUMN_DATA_SOURCE => Types::JSON,
                ]
            );
        }
    }
}
