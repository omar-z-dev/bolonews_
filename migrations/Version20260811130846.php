<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260811130846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY `FK_23A0E6660BB6FE6`');
        $this->addSql('ALTER TABLE article CHANGE auteur_id auteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6660BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE article_like DROP FOREIGN KEY `FK_1C21C7B2FB88E14F`');
        $this->addSql('ALTER TABLE article_like CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article_like ADD CONSTRAINT FK_1C21C7B2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6660BB6FE6');
        $this->addSql('ALTER TABLE article CHANGE auteur_id auteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT `FK_23A0E6660BB6FE6` FOREIGN KEY (auteur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE article_like DROP FOREIGN KEY FK_1C21C7B2FB88E14F');
        $this->addSql('ALTER TABLE article_like CHANGE utilisateur_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE article_like ADD CONSTRAINT `FK_1C21C7B2FB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
