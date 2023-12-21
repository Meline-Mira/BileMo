<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20231221101320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Column changes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE picture ADD description VARCHAR(150) NOT NULL, DROP descritpion');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(60) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE picture ADD descritpion LONGTEXT NOT NULL, DROP description');
    }
}
