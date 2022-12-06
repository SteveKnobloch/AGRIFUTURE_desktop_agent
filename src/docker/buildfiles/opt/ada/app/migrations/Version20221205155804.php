<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221205155804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE upload ADD COLUMN error INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE upload ADD COLUMN uploaded BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__upload AS SELECT id, analysis_id, file_name FROM upload');
        $this->addSql('DROP TABLE upload');
        $this->addSql('CREATE TABLE upload (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, analysis_id BLOB NOT NULL --(DC2Type:uuid)
        , file_name VARCHAR(255) NOT NULL, CONSTRAINT FK_17BDE61F7941003F FOREIGN KEY (analysis_id) REFERENCES analysis (local_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO upload (id, analysis_id, file_name) SELECT id, analysis_id, file_name FROM __temp__upload');
        $this->addSql('DROP TABLE __temp__upload');
        $this->addSql('CREATE INDEX IDX_17BDE61F7941003F ON upload (analysis_id)');
    }
}
