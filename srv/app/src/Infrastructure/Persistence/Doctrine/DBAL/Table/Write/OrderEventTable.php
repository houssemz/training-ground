<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\DBAL\Table\Write;

use App\Infrastructure\Persistence\Doctrine\DBAL\Table\DoctrineDBALTableSchemaConfigurator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;

class OrderEventTable implements DoctrineDBALTableSchemaConfigurator
{
    public const TABLE_NAME = 'write_order_event';
    public const COLUMN_EVENT_ID = 'event_id';
    public const COLUMN_AGGREGATE_ROOT_ID = 'aggregate_root_id';
    public const COLUMN_AGGREGATE_ROOT_VERSION = 'aggregate_root_version';
    public const COLUMN_EVENT_NAME = 'event_name';
    public const COLUMN_OCCURRED_ON = 'occurred_on';
    public const COLUMN_RECORDED_ON = 'recorded_on';
    public const COLUMN_DATA_SOURCE = 'data_source';

    public function tableName(): string
    {
        return self::TABLE_NAME;
    }

    public function tableSchema(Table $schema): void
    {
        $schema
            ->addColumn(self::COLUMN_EVENT_ID, 'uuid')
        ;

        $schema
        ->addColumn(self::COLUMN_AGGREGATE_ROOT_ID, 'uuid')
        ;

        $schema
            ->addColumn(self::COLUMN_AGGREGATE_ROOT_VERSION, Types::INTEGER)
            ->setUnsigned(true)
        ;

        $schema
            ->addColumn(self::COLUMN_EVENT_NAME, Types::STRING)
        ;

        $schema
            ->addColumn(self::COLUMN_OCCURRED_ON, Types::DATETIMETZ_IMMUTABLE)
        ;

        $schema
            ->addColumn(self::COLUMN_RECORDED_ON, Types::DATETIMETZ_IMMUTABLE)
        ;

        $schema
            ->addColumn(self::COLUMN_DATA_SOURCE, Types::JSON)
            ->setPlatformOption('jsonb', true)
        ;

        $schema->setPrimaryKey([self::COLUMN_EVENT_ID]);
        $schema->addUniqueIndex([self::COLUMN_AGGREGATE_ROOT_ID, self::COLUMN_AGGREGATE_ROOT_VERSION]);
        $schema->addIndex([self::COLUMN_RECORDED_ON]);
    }
}
