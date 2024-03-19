<?php

declare(strict_types=1);

namespace App\Application\Query\OrderDetails\GetDetails;

use App\Domain\Read\OrderDetails\OrderDetails;
use App\Domain\Write\Order\Store\OrderStore;

class GetDetailsHandler
{
    public function __construct(
        private OrderStore $orderStore
    ) {
    }

    public function __invoke(GetDetails $query): ?OrderDetails
    {
        if (!$this->orderStore->has($query->id)) {
            return null;
        }

        return new OrderDetails($this->orderStore->load($query->id));
    }
}
