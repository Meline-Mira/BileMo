<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20231214135814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creation of database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE client_user (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, INDEX IDX_5C0F152BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, price NUMERIC(7, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, phone_id INT NOT NULL, url LONGTEXT NOT NULL, descritpion LONGTEXT NOT NULL, INDEX IDX_16DB4F893B7323CB (phone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client_user ADD CONSTRAINT FK_5C0F152BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F893B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE client_user DROP FOREIGN KEY FK_5C0F152BA76ED395');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F893B7323CB');
        $this->addSql('DROP TABLE client_user');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE `user`');
    }
}
