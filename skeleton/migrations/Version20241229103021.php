<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229103021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, patronumic VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE wall (id UUID NOT NULL, author_id UUID NOT NULL, action VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_13F5EFF6F675F31B ON wall (author_id)');
        $this->addSql('COMMENT ON COLUMN wall.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN wall.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE wall ADD CONSTRAINT FK_13F5EFF6F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE wall DROP CONSTRAINT FK_13F5EFF6F675F31B');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE wall');
    }
}
