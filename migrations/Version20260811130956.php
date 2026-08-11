<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260811130956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY `FK_67F068BC60BB6FE6`');
        $this->addSql('ALTER TABLE commentaire CHANGE auteur_id auteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC60BB6FE6');
        $this->addSql('ALTER TABLE commentaire CHANGE auteur_id auteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT `FK_67F068BC60BB6FE6` FOREIGN KEY (auteur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
