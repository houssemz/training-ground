<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations\Schema;

use App\Infrastructure\Persistence\Doctrine\DBAL\Table\DoctrineDBALTableSchemaConfigurator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

class DoctrineMigrationSchemaProvider implements SchemaProvider
{
    /** @param iterable<DoctrineDBALTableSchemaConfigurator> $tableSchemaConfigurators */
    public function __construct(
        private readonly iterable $tableSchemaConfigurators
    ) {
    }

    public function createSchema(): Schema
    {
        $schema = new Schema();
        $schema->createNamespace('public'); // Fix empty migration generation

        /** @var DoctrineDBALTableSchemaConfigurator $tableSchemaConfigurator */
        foreach ($this->tableSchemaConfigurators as $tableSchemaConfigurator) {
            $tableSchemaConfigurator->tableSchema($schema->createTable($tableSchemaConfigurator->tableName()));
        }

        return $schema;
    }
}
