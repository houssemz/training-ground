<?php

namespace App\Tests\PHPUnit\UserInterface\Worker\Order\CreateOrder;
use App\Infrastructure\Persistence\Doctrine\DBAL\Table\Write\OrderEventTable;
use App\Tests\PHPUnit\Shared\TestCase\ApiTestCase;
use App\Tests\PHPUnit\Shared\TestCase\MessagingTestCase;
use App\UserInterface\Worker\Order\CreateOrder\CreateOrderPayload;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class CreateOrderWorkerTest extends MessagingTestCase
{

    private const ORDER_REFERENCE= 'reference1';
    private const LOADING_IDENTIFIER = 'place_123';
    private const DELIVERY_IDENTIFIER = 'place_456';
    private const CREATED_AT = '2024-03-19T15:00:00+00:00';

    public function testHandleCreateOrderPayload()
    {
        $payload = new CreateOrderPayload(
            reference: self::ORDER_REFERENCE,
            loadingIdentifier: self::LOADING_IDENTIFIER,
            deliveryIdentifier: self::DELIVERY_IDENTIFIER,
            creationDate: \DateTimeImmutable::createFromFormat(\DATE_ATOM, self::CREATED_AT)
        );

        $this->transport()->send($payload);
        $this->transport()->process();
        $orderId = $this->connectionService()->createQueryBuilder()
            ->select(OrderEventTable::COLUMN_AGGREGATE_ROOT_ID)
            ->from(OrderEventTable::TABLE_NAME)
            ->executeQuery()
            ->fetchOne()
            ;

        $this->assertNotFalse($orderId);
    }
}
