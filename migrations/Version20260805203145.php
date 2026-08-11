<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260805203145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_like (id INT AUTO_INCREMENT NOT NULL, date_creation DATETIME NOT NULL, utilisateur_id INT DEFAULT NULL, article_id INT DEFAULT NULL, INDEX IDX_1C21C7B2FB88E14F (utilisateur_id), INDEX IDX_1C21C7B27294869C (article_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE article_like ADD CONSTRAINT FK_1C21C7B2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article_like ADD CONSTRAINT FK_1C21C7B27294869C FOREIGN KEY (article_id) REFERENCES article (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_like DROP FOREIGN KEY FK_1C21C7B2FB88E14F');
        $this->addSql('ALTER TABLE article_like DROP FOREIGN KEY FK_1C21C7B27294869C');
        $this->addSql('DROP TABLE article_like');
    }
}
