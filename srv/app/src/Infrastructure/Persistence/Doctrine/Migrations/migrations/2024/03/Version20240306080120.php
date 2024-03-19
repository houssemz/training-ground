<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240306080120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create order event table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE write_order_event (event_id UUID NOT NULL, aggregate_root_id UUID NOT NULL, aggregate_root_version INT NOT NULL, event_name VARCHAR(255) NOT NULL, occurred_on TIMESTAMP(0) WITH TIME ZONE NOT NULL, recorded_on TIMESTAMP(0) WITH TIME ZONE NOT NULL, data_source JSONB NOT NULL, PRIMARY KEY(event_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9C04D8AB745C37BA70670451 ON write_order_event (aggregate_root_id, aggregate_root_version)');
        $this->addSql('CREATE INDEX IDX_9C04D8ABB4A9DC01 ON write_order_event (recorded_on)');
        $this->addSql('COMMENT ON COLUMN write_order_event.event_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN write_order_event.aggregate_root_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN write_order_event.occurred_on IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN write_order_event.recorded_on IS \'(DC2Type:datetimetz_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
