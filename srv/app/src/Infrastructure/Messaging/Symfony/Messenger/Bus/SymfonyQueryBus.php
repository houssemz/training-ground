<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging\Symfony\Messenger\Bus;

use App\Application\Query\Query;
use App\Application\Query\QueryBus;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyQueryBus implements QueryBus
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    public function ask(Query $query): mixed
    {
        return $this->handle($query);
    }
}
