<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\DBAL\Table;

use Doctrine\DBAL\Schema\Table;

interface DoctrineDBALTableSchemaConfigurator
{
    public function tableName(): string;

    public function tableSchema(Table $schema): void;
}
