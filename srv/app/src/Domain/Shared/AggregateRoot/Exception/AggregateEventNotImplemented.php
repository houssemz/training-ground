<?php

declare(strict_types=1);

namespace App\Domain\Shared\AggregateRoot\Exception;

class AggregateEventNotImplemented extends \DomainException implements AggregateRootException
{
    public function __construct(string $event)
    {
        $message = sprintf('Aggregate event %s not implemented', $event);
        parent::__construct($message);
    }
}
