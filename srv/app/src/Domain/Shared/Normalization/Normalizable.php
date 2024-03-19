<?php

declare(strict_types=1);

namespace App\Domain\Shared\Normalization;

interface Normalizable
{
    public static function denormalize(mixed $data): self;

    /** @return null|array<int|string, mixed>|bool|float|int|string */
    public function normalize(): array|string|int|float|bool|null;
}
