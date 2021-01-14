<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025225729 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pictures DROP FOREIGN KEY FK_3F0FE980A76ED395');
        $this->addSql('DROP INDEX idx_3f0fe980a76ed395 ON pictures');
        $this->addSql('CREATE INDEX IDX_8F7C2FC0A76ED395 ON pictures (user_id)');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_3F0FE980A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD is_verified TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC0A76ED395');
        $this->addSql('DROP INDEX idx_8f7c2fc0a76ed395 ON pictures');
        $this->addSql('CREATE INDEX IDX_3F0FE980A76ED395 ON pictures (user_id)');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users DROP is_verified');
    }
}
