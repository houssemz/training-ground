<?php

declare(strict_types=1);

namespace App\Domain\Write;

use App\Domain\Shared\Normalization\Normalizable;
use Symfony\Component\Uid\Uuid;

abstract class AbstractEvent implements Normalizable
{
    protected \DateTimeImmutable $recordedOn;

    public function __construct(
        protected Uuid $id,
        protected Uuid $aggregateId,
        protected int $version,
        protected \DateTimeImmutable $occurredOn,
    ) {
        $this->recordedOn = new \DateTimeImmutable();
    }

    abstract public static function name(): string;

    public function id(): Uuid
    {
        return $this->id;
    }

    public function aggregateId(): Uuid
    {
        return $this->aggregateId;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function recordedOn(): \DateTimeImmutable
    {
        return $this->recordedOn;
    }
}
