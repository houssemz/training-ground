<?php

declare(strict_types=1);

namespace App\Tests\PHPUnit\Shared\Persistence;

use Doctrine\DBAL\Connection;

trait TransactionalBehavior
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->connectionService()->beginTransaction();
    }

    public function tearDown(): void
    {
        if (static::$booted) {
            $connection = $this->connectionService();

            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
        }

        parent::tearDown();
    }

    protected function connectionService(): Connection
    {
        return $this->get(Connection::class);
    }
}
