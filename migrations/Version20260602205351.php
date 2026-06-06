<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260602205351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, achat INT NOT NULL, montant_total DOUBLE PRECISION NOT NULL, date_commande DATE NOT NULL, statut VARCHAR(255) NOT NULL, evenement_id_id INT NOT NULL, user_id_id INT NOT NULL, INDEX IDX_35D4282CECEE32AF (evenement_id_id), INDEX IDX_35D4282C9D86650F (user_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE evenements (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date DATE NOT NULL, lieu VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, nombres_places INT NOT NULL, prix_ticket DOUBLE PRECISION NOT NULL, user_id_id INT NOT NULL, INDEX IDX_E10AD4009D86650F (user_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tickets (id INT AUTO_INCREMENT NOT NULL, code_unique VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, date_achat DATE NOT NULL, evenement_id_id INT NOT NULL, user_id_id INT NOT NULL, INDEX IDX_54469DF4ECEE32AF (evenement_id_id), INDEX IDX_54469DF49D86650F (user_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282CECEE32AF FOREIGN KEY (evenement_id_id) REFERENCES evenements (id)');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282C9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE evenements ADD CONSTRAINT FK_E10AD4009D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4ECEE32AF FOREIGN KEY (evenement_id_id) REFERENCES evenements (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF49D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282CECEE32AF');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282C9D86650F');
        $this->addSql('ALTER TABLE evenements DROP FOREIGN KEY FK_E10AD4009D86650F');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF4ECEE32AF');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF49D86650F');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE evenements');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
