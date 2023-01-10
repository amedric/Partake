<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230107230004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD idea_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5B6FEF7D FOREIGN KEY (idea_id) REFERENCES idea (id)');
        $this->addSql('CREATE INDEX IDX_9474526C5B6FEF7D ON comment (idea_id)');
        $this->addSql('ALTER TABLE project ADD is_archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD team VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5B6FEF7D');
        $this->addSql('DROP INDEX IDX_9474526C5B6FEF7D ON comment');
        $this->addSql('ALTER TABLE comment DROP idea_id');
        $this->addSql('ALTER TABLE project DROP is_archived');
        $this->addSql('ALTER TABLE user DROP team');
    }
}
