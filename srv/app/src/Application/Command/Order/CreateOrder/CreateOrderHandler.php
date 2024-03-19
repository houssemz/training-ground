<?php

declare(strict_types=1);

namespace App\Application\Command\Order\CreateOrder;

use App\Domain\Shared\Order\OrderReference;
use App\Domain\Shared\Order\Place;
use App\Domain\Write\Order\Order;
use App\Domain\Write\Order\Store\OrderStore;

class CreateOrderHandler
{
    public function __construct(
        private OrderStore $orderStore,
    ) {
    }

    public function __invoke(CreateOrder $command): void
    {
        // TODO check uniqueness order reference

        $order = Order::with(
            reference: new OrderReference($command->orderReference),
            creationDate: $command->creationDate,
            loading: new Place($command->loadingIdentifier),
            delivery: new Place($command->deliveryIdentifier)
        );

        $this->orderStore->save($order);
    }
}
