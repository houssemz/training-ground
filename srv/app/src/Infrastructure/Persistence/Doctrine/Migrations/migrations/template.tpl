<?php

declare(strict_types=1);

namespace <namespace>;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class <className> extends AbstractMigration
{
    public function getDescription(): string
    {
//        return 'shippeo/shippeo.issues#1234: Create new table "foo".';
    }

    public function up(Schema $schema): void
    {
<up>
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}