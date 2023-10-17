<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231017034334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE access_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE access_token (id VARCHAR(255) NOT NULL, gift_list_id INT NOT NULL, token VARCHAR(64) NOT NULL, used BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6A2DD6851F42524 ON access_token (gift_list_id)');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6851F42524 FOREIGN KEY (gift_list_id) REFERENCES gift_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE access_token_id_seq CASCADE');
        $this->addSql('ALTER TABLE access_token DROP CONSTRAINT FK_B6A2DD6851F42524');
        $this->addSql('DROP TABLE access_token');
    }
}
