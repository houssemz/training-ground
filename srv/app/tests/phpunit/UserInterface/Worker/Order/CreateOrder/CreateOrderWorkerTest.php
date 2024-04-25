<?php

declare(strict_types=1);

namespace App\Tests\PHPUnit\UserInterface\Worker\Order\CreateOrder;

use App\Infrastructure\Persistence\Doctrine\DBAL\Table\Write\OrderEventTable;
use App\Tests\PHPUnit\Shared\TestCase\MessagingTestCase;
use App\UserInterface\Worker\Order\CreateOrder\CreateOrderPayload;
use Doctrine\DBAL\Exception;

class CreateOrderWorkerTest extends MessagingTestCase
{
    private const string ORDER_REFERENCE = 'reference1';
    private const string LOADING_IDENTIFIER = 'place_123';
    private const string DELIVERY_IDENTIFIER = 'place_456';
    private const string CREATED_AT = '2024-03-19T15:00:00+00:00';

    /**
     * @throws Exception
     */
    public function testHandleCreateOrderPayload(): void
    {
        $createDate = \DateTimeImmutable::createFromFormat(\DATE_ATOM, self::CREATED_AT);

        $payload = new CreateOrderPayload(
            reference: self::ORDER_REFERENCE,
            loadingIdentifier: self::LOADING_IDENTIFIER,
            deliveryIdentifier: self::DELIVERY_IDENTIFIER,
            creationDate: $createDate
        );

        $this->transport()->send($payload);
        $this->transport()->process();

        $order = $this->connectionService()->createQueryBuilder()
            ->select(OrderEventTable::COLUMN_AGGREGATE_ROOT_ID)
            ->addSelect(OrderEventTable::COLUMN_EVENT_NAME)
            ->addSelect(OrderEventTable::COLUMN_OCCURRED_ON)
            ->from(OrderEventTable::TABLE_NAME)
            ->executeQuery()
            ->fetchAssociative()
        ;

        self::assertEquals(
            expected: [
                'aggregate_root_id' => 'f8cea8fd-a687-40d6-8959-9160fab35ec3',
                'occurred_on' => '2024-03-19 15:00:00+00',
                'event_name' => 'order_created',
            ],
            actual: $order
        );
    }
}
