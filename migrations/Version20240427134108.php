<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240427134108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan_alim ADD CONSTRAINT FK_71F97CC7CF538BDF FOREIGN KEY (nut_id) REFERENCES nutrition (id)');
        $this->addSql('ALTER TABLE plan_alim ADD CONSTRAINT FK_71F97CC76B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE plan_alim ADD CONSTRAINT FK_71F97CC78CB1FF91 FOREIGN KEY (id_regime) REFERENCES regime (id)');
        $this->addSql('CREATE INDEX IDX_71F97CC7CF538BDF ON plan_alim (nut_id)');
        $this->addSql('CREATE INDEX IDX_71F97CC76B3CA4B ON plan_alim (id_user)');
        $this->addSql('ALTER TABLE recette CHANGE id_ingredients id_ingredients VARCHAR(256) NOT NULL');
        $this->addSql('ALTER TABLE regime DROP FOREIGN KEY regime_ibfk_1');
        $this->addSql('ALTER TABLE regime CHANGE id_repas id_repas INT DEFAULT NULL, CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE regime ADD CONSTRAINT FK_AA864A7C46561083 FOREIGN KEY (id_repas) REFERENCES repas (id)');
        $this->addSql('ALTER TABLE repas ADD star_rating INT NOT NULL, CHANGE image image VARCHAR(300) NOT NULL');
        $this->addSql('ALTER TABLE repas ADD CONSTRAINT FK_A8D351B39726CAE0 FOREIGN KEY (id_recette) REFERENCES recette (id)');
        $this->addSql('ALTER TABLE repas ADD CONSTRAINT FK_A8D351B36B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('DROP INDEX id_recette ON repas');
        $this->addSql('CREATE INDEX IDX_A8D351B39726CAE0 ON repas (id_recette)');
        $this->addSql('DROP INDEX id_user ON repas');
        $this->addSql('CREATE INDEX IDX_A8D351B36B3CA4B ON repas (id_user)');
        $this->addSql('ALTER TABLE request_nut DROP FOREIGN KEY request_nut_ibfk_1');
        $this->addSql('ALTER TABLE request_nut CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE request_nut ADD CONSTRAINT FK_750246E2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan_alim DROP FOREIGN KEY FK_71F97CC7CF538BDF');
        $this->addSql('ALTER TABLE plan_alim DROP FOREIGN KEY FK_71F97CC76B3CA4B');
        $this->addSql('ALTER TABLE plan_alim DROP FOREIGN KEY FK_71F97CC78CB1FF91');
        $this->addSql('DROP INDEX IDX_71F97CC7CF538BDF ON plan_alim');
        $this->addSql('DROP INDEX IDX_71F97CC76B3CA4B ON plan_alim');
        $this->addSql('ALTER TABLE recette CHANGE id_ingredients id_ingredients VARCHAR(256) NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE regime DROP FOREIGN KEY FK_AA864A7C46561083');
        $this->addSql('ALTER TABLE regime CHANGE id_repas id_repas INT NOT NULL, CHANGE date date DATE DEFAULT \'CURRENT_TIMESTAMP\' NOT NULL');
        $this->addSql('ALTER TABLE regime ADD CONSTRAINT regime_ibfk_1 FOREIGN KEY (id_repas) REFERENCES repas (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repas DROP FOREIGN KEY FK_A8D351B39726CAE0');
        $this->addSql('ALTER TABLE repas DROP FOREIGN KEY FK_A8D351B36B3CA4B');
        $this->addSql('ALTER TABLE repas DROP FOREIGN KEY FK_A8D351B39726CAE0');
        $this->addSql('ALTER TABLE repas DROP FOREIGN KEY FK_A8D351B36B3CA4B');
        $this->addSql('ALTER TABLE repas DROP star_rating, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX idx_a8d351b39726cae0 ON repas');
        $this->addSql('CREATE INDEX id_recette ON repas (id_recette)');
        $this->addSql('DROP INDEX idx_a8d351b36b3ca4b ON repas');
        $this->addSql('CREATE INDEX id_user ON repas (id_user)');
        $this->addSql('ALTER TABLE repas ADD CONSTRAINT FK_A8D351B39726CAE0 FOREIGN KEY (id_recette) REFERENCES recette (id)');
        $this->addSql('ALTER TABLE repas ADD CONSTRAINT FK_A8D351B36B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE request_nut DROP FOREIGN KEY FK_750246E2A76ED395');
        $this->addSql('ALTER TABLE request_nut CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE request_nut ADD CONSTRAINT request_nut_ibfk_1 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
