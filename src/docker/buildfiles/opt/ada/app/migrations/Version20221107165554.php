<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221107165554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE token (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, token VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE analysis (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, remote_pipeline_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, local_uuid VARCHAR(36) NOT NULL, paused BOOLEAN NOT NULL, relative_data_path VARCHAR(1024) NOT NULL, file_type VARCHAR(5) NOT NULL)');
        $this->addSql('CREATE TABLE upload (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, analysis_id INTEGER NOT NULL, file_name VARCHAR(255) NOT NULL, CONSTRAINT FK_17BDE61F7941003F FOREIGN KEY (analysis_id) REFERENCES analysis (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_17BDE61F7941003F ON upload (analysis_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE upload');
        $this->addSql('DROP TABLE analysis');
        $this->addSql('DROP TABLE token');
    }
}
