<?php

declare(strict_types=1);

namespace App\Application\Query;

interface QueryBus
{
    public function ask(Query $command): mixed;
}
