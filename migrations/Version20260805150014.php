<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260805150014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY `FK_23A0E667A2E475A`');
        $this->addSql('DROP INDEX IDX_23A0E667A2E475A ON article');
        $this->addSql('ALTER TABLE article CHANGE catégorie_id categorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66BCF5E72D ON article (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66BCF5E72D');
        $this->addSql('DROP INDEX IDX_23A0E66BCF5E72D ON article');
        $this->addSql('ALTER TABLE article CHANGE categorie_id catégorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT `FK_23A0E667A2E475A` FOREIGN KEY (catégorie_id) REFERENCES categorie (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_23A0E667A2E475A ON article (catégorie_id)');
    }
}
