<?php

declare(strict_types=1);

namespace App\UserInterface\Cli\Mock;

use App\Application\Command\CommandBus;
use App\Application\Command\Order\CreateOrder\CreateOrder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This cli is used to simulate the sending of a creation order message from the outside.
 */
#[AsCommand(
    name: 'app:mock:order-create',
    description: 'This cli is used to simulate the sending of a create order message from the outside.'
)]
class CreateOrderCli extends Command
{
    public const string DATE_FORMAT = 'Y-m-d';

    public function __construct(
        private readonly CommandBus $workerBus,
    ) {
        parent::__construct();
    }

    #[\Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $creationDateTime = $this->checkCreationDate($input->getOption('creationDate'));

            $this->workerBus->execute(new CreateOrder(
                orderReference: 'Reference1',
                loadingIdentifier: 'place123',
                deliveryIdentifier: 'place456',
                creationDate: $creationDateTime
            ));

            $output->writeln('<info>Command is created with success.</info>');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }

    #[\Override]
    protected function configure(): void
    {
        // in the "future", `creationDate` maybe will be got from another service
        $this->addOption('creationDate', 'c', InputOption::VALUE_OPTIONAL, sprintf('Creation date of the order (%s)', self::DATE_FORMAT));
    }

    private function checkCreationDate(mixed $creationDate): \DateTimeImmutable
    {
        if (!isset($creationDate)) {
            return new \DateTimeImmutable();
        }

        if (!\is_string($creationDate)) {
            throw new \InvalidArgumentException('Creation date must be a string.');
        }

        $creationDateTime = \DateTimeImmutable::createFromFormat(format: self::DATE_FORMAT, datetime: $creationDate);
        if (!$creationDateTime instanceof \DateTimeImmutable) {
            throw new \InvalidArgumentException(sprintf('Invalid creation date format. Please provide a date in the format %s', self::DATE_FORMAT));
        }

        return $creationDateTime;
    }
}
