<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260805095042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, image VARCHAR(255) NOT NULL, date_modification DATETIME DEFAULT NULL, catégorie_id INT NOT NULL, auteur_id INT NOT NULL, INDEX IDX_23A0E667A2E475A (catégorie_id), INDEX IDX_23A0E6660BB6FE6 (auteur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E667A2E475A FOREIGN KEY (catégorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6660BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E667A2E475A');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6660BB6FE6');
        $this->addSql('DROP TABLE article');
    }
}
