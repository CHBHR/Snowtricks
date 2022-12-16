<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221216161522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure CHANGE nom nom VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE groupe CHANGE titre titre VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE images CHANGE nom nom VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur CHANGE email email VARCHAR(50) NOT NULL, CHANGE nom_utilisateur nom_utilisateur VARCHAR(20) NOT NULL, CHANGE mot_de_passe mot_de_passe VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3D37CC8AC ON utilisateur (nom_utilisateur)');
        $this->addSql('ALTER TABLE video CHANGE description description VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure CHANGE nom nom VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE groupe CHANGE titre titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE images CHANGE nom nom VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_1D1C63B3D37CC8AC ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur CHANGE email email VARCHAR(100) NOT NULL, CHANGE nom_utilisateur nom_utilisateur VARCHAR(255) NOT NULL, CHANGE mot_de_passe mot_de_passe VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE video CHANGE description description VARCHAR(255) DEFAULT NULL');
    }
}
