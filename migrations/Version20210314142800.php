<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210314142800 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attachment (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post ADD attachment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D464E68B ON post (attachment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D464E68B');
        $this->addSql('DROP TABLE attachment');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D464E68B ON post');
        $this->addSql('ALTER TABLE post DROP attachment_id');
    }
}
