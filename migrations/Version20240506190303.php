<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240506190303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD repas_id INT DEFAULT NULL, DROP id_repas');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D1D236AAA FOREIGN KEY (repas_id) REFERENCES repas (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D1D236AAA ON commande (repas_id)');
        $this->addSql('ALTER TABLE livraison CHANGE id_commande id_commande INT DEFAULT NULL');
        $this->addSql('ALTER TABLE livraison ADD CONSTRAINT FK_A60C9F1F3E314AE8 FOREIGN KEY (id_commande) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_A60C9F1F3E314AE8 ON livraison (id_commande)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D1D236AAA');
        $this->addSql('DROP INDEX IDX_6EEAA67D1D236AAA ON commande');
        $this->addSql('ALTER TABLE commande ADD id_repas VARCHAR(300) NOT NULL, DROP repas_id');
        $this->addSql('ALTER TABLE livraison DROP FOREIGN KEY FK_A60C9F1F3E314AE8');
        $this->addSql('DROP INDEX IDX_A60C9F1F3E314AE8 ON livraison');
        $this->addSql('ALTER TABLE livraison CHANGE id_commande id_commande INT NOT NULL');
    }
}
