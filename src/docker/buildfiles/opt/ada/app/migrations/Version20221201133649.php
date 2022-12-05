<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221201133649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Use native UUID type and use that as primary ID';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__analysis AS SELECT remote_pipeline_id, name, local_uuid, paused, relative_data_path, file_type FROM analysis');
        $this->addSql('DROP TABLE analysis');
        $this->addSql('CREATE TABLE analysis (local_uuid BLOB NOT NULL --(DC2Type:uuid)
        , remote_pipeline_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, paused BOOLEAN NOT NULL, relative_data_path VARCHAR(1024) NOT NULL, file_type VARCHAR(5) NOT NULL, PRIMARY KEY(local_uuid))');
        $this->addSql('INSERT INTO analysis (remote_pipeline_id, name, local_uuid, paused, relative_data_path, file_type) SELECT remote_pipeline_id, name, local_uuid, paused, relative_data_path, file_type FROM __temp__analysis');
        $this->addSql('DROP TABLE __temp__analysis');
        $this->addSql('CREATE TEMPORARY TABLE __temp__upload AS SELECT id, analysis_id, file_name FROM upload');
        $this->addSql('DROP TABLE upload');
        $this->addSql('CREATE TABLE upload (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, analysis_id BLOB NOT NULL --(DC2Type:uuid)
        , file_name VARCHAR(255) NOT NULL, CONSTRAINT FK_17BDE61F7941003F FOREIGN KEY (analysis_id) REFERENCES analysis (local_uuid) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO upload (id, analysis_id, file_name) SELECT id, analysis_id, file_name FROM __temp__upload');
        $this->addSql('DROP TABLE __temp__upload');
        $this->addSql('CREATE INDEX IDX_17BDE61F7941003F ON upload (analysis_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__analysis AS SELECT local_uuid, remote_pipeline_id, name, paused, relative_data_path, file_type FROM analysis');
        $this->addSql('DROP TABLE analysis');
        $this->addSql('CREATE TABLE analysis (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, local_uuid VARCHAR(36) NOT NULL, remote_pipeline_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, paused BOOLEAN NOT NULL, relative_data_path VARCHAR(1024) NOT NULL, file_type VARCHAR(5) NOT NULL)');
        $this->addSql('INSERT INTO analysis (local_uuid, remote_pipeline_id, name, paused, relative_data_path, file_type) SELECT local_uuid, remote_pipeline_id, name, paused, relative_data_path, file_type FROM __temp__analysis');
        $this->addSql('DROP TABLE __temp__analysis');
        $this->addSql('CREATE TEMPORARY TABLE __temp__upload AS SELECT id, analysis_id, file_name FROM upload');
        $this->addSql('DROP TABLE upload');
        $this->addSql('CREATE TABLE upload (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, analysis_id INTEGER NOT NULL, file_name VARCHAR(255) NOT NULL, CONSTRAINT FK_17BDE61F7941003F FOREIGN KEY (analysis_id) REFERENCES analysis (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO upload (id, analysis_id, file_name) SELECT id, analysis_id, file_name FROM __temp__upload');
        $this->addSql('DROP TABLE __temp__upload');
        $this->addSql('CREATE INDEX IDX_17BDE61F7941003F ON upload (analysis_id)');
    }
}
