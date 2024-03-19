<?php

declare(strict_types=1);

namespace App\Domain\Write\Order\Store;

use App\Domain\Write\Order\Event\OrderEvent;
use App\Domain\Write\Order\Order;
use Symfony\Component\Uid\Uuid;

interface OrderStore
{
    public function save(Order $order): void;

    /** @return \Traversable<OrderEvent> */
    public function load(Uuid $orderId): \Traversable;

    public function has(Uuid $orderId): bool;
}
