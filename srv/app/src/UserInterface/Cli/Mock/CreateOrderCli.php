<?php

declare(strict_types=1);

namespace App\UserInterface\Cli\Mock;

use App\Application\Command\CommandBus;
use App\Application\Command\Order\CreateOrder\CreateOrder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This cli is used to simulate the sending of a create order message from the outside.
 */
#[AsCommand(
    name: 'app:mock:order-create',
    description: 'This cli is used to simulate the sending of a create order message from the outside.'
)]
class CreateOrderCli extends Command
{
    public function __construct(
        private CommandBus $workerBus,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->workerBus->execute(new CreateOrder(
            orderReference: 'Reference1',
            loadingIdentifier: 'place123',
            deliveryIdentifier: 'place456',
            creationDate: new \DateTimeImmutable('2024-03-11 09:00:00')
        ));

        return Command::SUCCESS;
    }
}
