<?php

namespace App\Tests\PHPUnit\UserInterface\Http\Order\GetDetails;

use App\Application\Command\Order\CreateOrder\CreateOrder;
use App\Application\Command\Order\CreateOrder\CreateOrderHandler;
use App\Infrastructure\Persistence\Doctrine\DBAL\Table\Write\OrderEventTable;
use App\Tests\PHPUnit\Shared\TestCase\ApiTestCase;

class GetDetailsControllerTest extends ApiTestCase
{

    private const ORDER_REFERENCE= 'reference1';
    private const LOADING_IDENTIFIER = 'place_123';
    private const DELIVERY_IDENTIFIER = 'place_456';
    private const CREATED_AT = '2024-03-19T15:00:00+00:00';

    public function testGetOrderDetailsFromCreated()
    {
        $command = new CreateOrder(
            orderReference: self::ORDER_REFERENCE,
            loadingIdentifier: self::LOADING_IDENTIFIER,
            deliveryIdentifier: self::DELIVERY_IDENTIFIER,
            creationDate: \DateTimeImmutable::createFromFormat(\DATE_ATOM, self::CREATED_AT)
        );

        $client = static::getClient();

        $handler = static::get(CreateOrderHandler::class);
        $handler($command);

        $orderId = $this->connectionService()->createQueryBuilder()
            ->select(OrderEventTable::COLUMN_AGGREGATE_ROOT_ID)
            ->from(OrderEventTable::TABLE_NAME)
            ->executeQuery()
            ->fetchOne()
            ;
        $this->assertNotFalse($orderId);
        $crawler = $client->request('GET', '/api/orders/' . $orderId);
        $this->thenTheValueAtJsonPathShouldBe('reference', self::ORDER_REFERENCE);
        $this->thenTheValueAtJsonPathShouldBe('createdAt', self::CREATED_AT);
        $this->thenTheValueAtJsonPathShouldBe('loading', self::LOADING_IDENTIFIER);
        $this->thenTheValueAtJsonPathShouldBe('delivery', self::DELIVERY_IDENTIFIER);
    }
}
