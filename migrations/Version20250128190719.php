<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250128190719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE agence_entity_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE agence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE agence (id INT NOT NULL, numero VARCHAR(25) NOT NULL, adresse VARCHAR(25) NOT NULL, telephone VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE agence_entity');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE agence_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE agence_entity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE agence_entity (id INT NOT NULL, numero VARCHAR(25) NOT NULL, adresse VARCHAR(50) NOT NULL, telephone VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE agence');
    }
}
