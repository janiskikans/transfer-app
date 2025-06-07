<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250607140457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create currency_rate table.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE currency_rate (id SERIAL NOT NULL, currency VARCHAR(3) NOT NULL, rate NUMERIC(12, 5) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_555B7C4D6956883F ON currency_rate (currency)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN currency_rate.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN currency_rate.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE currency_rate ADD CONSTRAINT FK_555B7C4D6956883F FOREIGN KEY (currency) REFERENCES currency (code) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE currency_rate DROP CONSTRAINT FK_555B7C4D6956883F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE currency_rate
        SQL);
    }
}
