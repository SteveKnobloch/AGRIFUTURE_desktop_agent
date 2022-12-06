<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206154908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__analysis AS SELECT local_uuid, remote_pipeline_id, name, relative_data_path, file_type FROM analysis');
        $this->addSql('DROP TABLE analysis');
        $this->addSql('CREATE TABLE analysis (local_uuid BLOB NOT NULL --(DC2Type:uuid)
        , remote_pipeline_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, relative_data_path VARCHAR(1024) NOT NULL, file_type VARCHAR(5) NOT NULL, status VARCHAR(255) DEFAULT \'running\' NOT NULL, PRIMARY KEY(local_uuid))');
        $this->addSql('INSERT INTO analysis (local_uuid, remote_pipeline_id, name, relative_data_path, file_type) SELECT local_uuid, remote_pipeline_id, name, relative_data_path, file_type FROM __temp__analysis');
        $this->addSql('DROP TABLE __temp__analysis');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__analysis AS SELECT local_uuid, remote_pipeline_id, name, relative_data_path, file_type FROM analysis');
        $this->addSql('DROP TABLE analysis');
        $this->addSql('CREATE TABLE analysis (local_uuid BLOB NOT NULL --(DC2Type:uuid)
        , remote_pipeline_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, relative_data_path VARCHAR(1024) NOT NULL, file_type VARCHAR(5) NOT NULL, paused BOOLEAN NOT NULL, PRIMARY KEY(local_uuid))');
        $this->addSql('INSERT INTO analysis (local_uuid, remote_pipeline_id, name, relative_data_path, file_type) SELECT local_uuid, remote_pipeline_id, name, relative_data_path, file_type FROM __temp__analysis');
        $this->addSql('DROP TABLE __temp__analysis');
    }
}
