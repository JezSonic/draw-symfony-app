<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251210110746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE draw_options (id VARCHAR(36) NOT NULL, content VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, draw_id VARCHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_9D777FEB6FC5C1B8 ON draw_options (draw_id)');
        $this->addSql('CREATE TABLE draw_results (id VARCHAR(36) NOT NULL, payload JSON DEFAULT NULL, draw_id VARCHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D2E161786FC5C1B8 ON draw_results (draw_id)');
        $this->addSql('CREATE TABLE draws (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, results_count INT NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE draw_options ADD CONSTRAINT FK_9D777FEB6FC5C1B8 FOREIGN KEY (draw_id) REFERENCES draws (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE draw_results ADD CONSTRAINT FK_D2E161786FC5C1B8 FOREIGN KEY (draw_id) REFERENCES draws (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE draw_options DROP CONSTRAINT FK_9D777FEB6FC5C1B8');
        $this->addSql('ALTER TABLE draw_results DROP CONSTRAINT FK_D2E161786FC5C1B8');
        $this->addSql('DROP TABLE draw_options');
        $this->addSql('DROP TABLE draw_results');
        $this->addSql('DROP TABLE draws');
    }
}
