<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503133343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livraison ADD CONSTRAINT FK_A60C9F1F3E314AE8 FOREIGN KEY (id_commande) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_A60C9F1F3E314AE8 ON livraison (id_commande)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livraison DROP FOREIGN KEY FK_A60C9F1F3E314AE8');
        $this->addSql('DROP INDEX IDX_A60C9F1F3E314AE8 ON livraison');
    }
}
