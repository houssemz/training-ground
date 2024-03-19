<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging\Symfony\Messenger\Bus;

use App\Application\Command\Command;
use App\Application\Command\CommandBus;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyCommandBus implements CommandBus
{
    public function __construct(
        private MessageBusInterface $commandBus
    ) {
    }

    public function execute(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
