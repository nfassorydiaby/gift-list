<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018061713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE access_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE booking_gift_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE gift_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE gift_list_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE gift_list_theme_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE access_token (id VARCHAR(255) NOT NULL, gift_list_id INT NOT NULL, token VARCHAR(64) NOT NULL, used BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6A2DD6851F42524 ON access_token (gift_list_id)');
        $this->addSql('CREATE TABLE booking_gift (id VARCHAR(255) NOT NULL, gift_id INT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BCEDBB43E7927C74 ON booking_gift (email)');
        $this->addSql('CREATE INDEX IDX_BCEDBB4397A95A83 ON booking_gift (gift_id)');
        $this->addSql('CREATE TABLE gift (id INT NOT NULL, gift_list_id INT NOT NULL, booked_by_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, image VARCHAR(255) DEFAULT NULL, lien_achat VARCHAR(255) NOT NULL, is_booked BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A47C990D51F42524 ON gift (gift_list_id)');
        $this->addSql('CREATE INDEX IDX_A47C990DF4A5BD90 ON gift (booked_by_id)');
        $this->addSql('CREATE TABLE gift_list (id INT NOT NULL, user_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description TEXT NOT NULL, is_private BOOLEAN NOT NULL, password VARCHAR(255) DEFAULT NULL, is_archived VARCHAR(255) NOT NULL, date_ouverture TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_fin_ouverture TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN NOT NULL, cover_name VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6B50A45A76ED395 ON gift_list (user_id)');
        $this->addSql('CREATE TABLE gift_list_gift_list_theme (gift_list_id INT NOT NULL, gift_list_theme_id INT NOT NULL, PRIMARY KEY(gift_list_id, gift_list_theme_id))');
        $this->addSql('CREATE INDEX IDX_614D72851F42524 ON gift_list_gift_list_theme (gift_list_id)');
        $this->addSql('CREATE INDEX IDX_614D728CCD9FB08 ON gift_list_gift_list_theme (gift_list_theme_id)');
        $this->addSql('CREATE TABLE gift_list_theme (id INT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, lastname VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, reset_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE user_gift_list (user_id INT NOT NULL, gift_list_id INT NOT NULL, PRIMARY KEY(user_id, gift_list_id))');
        $this->addSql('CREATE INDEX IDX_B65587A1A76ED395 ON user_gift_list (user_id)');
        $this->addSql('CREATE INDEX IDX_B65587A151F42524 ON user_gift_list (gift_list_id)');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6851F42524 FOREIGN KEY (gift_list_id) REFERENCES gift_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking_gift ADD CONSTRAINT FK_BCEDBB4397A95A83 FOREIGN KEY (gift_id) REFERENCES gift (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990D51F42524 FOREIGN KEY (gift_list_id) REFERENCES gift_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gift ADD CONSTRAINT FK_A47C990DF4A5BD90 FOREIGN KEY (booked_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gift_list ADD CONSTRAINT FK_B6B50A45A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gift_list_gift_list_theme ADD CONSTRAINT FK_614D72851F42524 FOREIGN KEY (gift_list_id) REFERENCES gift_list (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE gift_list_gift_list_theme ADD CONSTRAINT FK_614D728CCD9FB08 FOREIGN KEY (gift_list_theme_id) REFERENCES gift_list_theme (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_gift_list ADD CONSTRAINT FK_B65587A1A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_gift_list ADD CONSTRAINT FK_B65587A151F42524 FOREIGN KEY (gift_list_id) REFERENCES gift_list (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE access_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE booking_gift_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE gift_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE gift_list_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE gift_list_theme_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE access_token DROP CONSTRAINT FK_B6A2DD6851F42524');
        $this->addSql('ALTER TABLE booking_gift DROP CONSTRAINT FK_BCEDBB4397A95A83');
        $this->addSql('ALTER TABLE gift DROP CONSTRAINT FK_A47C990D51F42524');
        $this->addSql('ALTER TABLE gift DROP CONSTRAINT FK_A47C990DF4A5BD90');
        $this->addSql('ALTER TABLE gift_list DROP CONSTRAINT FK_B6B50A45A76ED395');
        $this->addSql('ALTER TABLE gift_list_gift_list_theme DROP CONSTRAINT FK_614D72851F42524');
        $this->addSql('ALTER TABLE gift_list_gift_list_theme DROP CONSTRAINT FK_614D728CCD9FB08');
        $this->addSql('ALTER TABLE user_gift_list DROP CONSTRAINT FK_B65587A1A76ED395');
        $this->addSql('ALTER TABLE user_gift_list DROP CONSTRAINT FK_B65587A151F42524');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE booking_gift');
        $this->addSql('DROP TABLE gift');
        $this->addSql('DROP TABLE gift_list');
        $this->addSql('DROP TABLE gift_list_gift_list_theme');
        $this->addSql('DROP TABLE gift_list_theme');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_gift_list');
    }
}
