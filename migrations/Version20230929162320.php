<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230929162320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD transporteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D97C86FA4 FOREIGN KEY (transporteur_id) REFERENCES transport (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D97C86FA4 ON commande (transporteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D97C86FA4');
        $this->addSql('DROP INDEX IDX_6EEAA67D97C86FA4 ON commande');
        $this->addSql('ALTER TABLE commande DROP transporteur_id');
    }
}
