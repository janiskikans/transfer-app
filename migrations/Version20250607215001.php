<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250607215001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add indexes to currency_rate, client, and account tables.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE INDEX account_created_at_idx ON account (created_at)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX idx_7d3656a419eb6921 RENAME TO account_client_id_idx
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX idx_7d3656a46956883f RENAME TO account_currency_idx
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX client_created_at_idx ON client (created_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX currency_source_idx ON currency_rate (source)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX currency_updated_at_idx ON currency_rate (updated_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX account_created_at_idx
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX account_currency_idx RENAME TO idx_7d3656a46956883f
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX account_client_id_idx RENAME TO idx_7d3656a419eb6921
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX currency_source_idx
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX currency_updated_at_idx
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX client_created_at_idx
        SQL);
    }
}
