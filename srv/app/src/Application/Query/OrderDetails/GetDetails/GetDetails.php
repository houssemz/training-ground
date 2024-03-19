<?php

declare(strict_types=1);

namespace App\Application\Query\OrderDetails\GetDetails;

use App\Application\Query\Query;
use Symfony\Component\Uid\Uuid;

readonly class GetDetails implements Query
{
    public function __construct(
        public Uuid $id,
    ) {
    }
}
