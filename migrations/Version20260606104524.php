<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260606104524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tickets_variation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, stock INT NOT NULL, evenement_id INT DEFAULT NULL, INDEX IDX_35121DB3FD02F13 (evenement_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE tickets_variation ADD CONSTRAINT FK_35121DB3FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenements (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tickets_variation DROP FOREIGN KEY FK_35121DB3FD02F13');
        $this->addSql('DROP TABLE tickets_variation');
    }
}
