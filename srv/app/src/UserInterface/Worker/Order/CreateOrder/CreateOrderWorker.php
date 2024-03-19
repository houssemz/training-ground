<?php

declare(strict_types=1);

namespace App\UserInterface\Worker\Order\CreateOrder;

use App\Application\Command\CommandBus;
use App\Application\Command\Order\CreateOrder\CreateOrder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateOrderWorker
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(CreateOrderPayload $payload): void
    {
        $this->commandBus->execute(new CreateOrder(
            orderReference: $payload->reference,
            loadingIdentifier: $payload->loadingIdentifier,
            deliveryIdentifier: $payload->deliveryIdentifier,
            creationDate: $payload->creationDate
        ));
    }
}
