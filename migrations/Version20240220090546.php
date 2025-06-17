<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220090546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('CREATE TABLE accreditation (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, participation_id INT DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, INDEX IDX_3BF9D0D871F7E88B (event_id), INDEX IDX_3BF9D0D86ACE3B73 (participation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('ALTER TABLE accreditation ADD CONSTRAINT FK_3BF9D0D871F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE accreditation ADD CONSTRAINT FK_3BF9D0D86ACE3B73 FOREIGN KEY (participation_id) REFERENCES participation (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE oauth2_access_token CHANGE client client VARCHAR(32) NOT NULL');
//        $this->addSql('ALTER TABLE oauth2_authorization_code CHANGE client client VARCHAR(32) NOT NULL');
//        $this->addSql('ALTER TABLE oauth2_client CHANGE identifier identifier VARCHAR(32) NOT NULL');
//        $this->addSql('ALTER TABLE participant_section CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('ALTER TABLE participation CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('ALTER TABLE partner CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT NOT NULL');
//        $this->addSql('ALTER TABLE section CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_73E858EE5E237E06 ON sector_pic (name)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_73E858EE989D9B62 ON sector_pic (slug)');
//        $this->addSql('CREATE INDEX slug_idx ON sector_pic (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE accreditation DROP FOREIGN KEY FK_3BF9D0D871F7E88B');
//        $this->addSql('ALTER TABLE accreditation DROP FOREIGN KEY FK_3BF9D0D86ACE3B73');
//        $this->addSql('DROP TABLE accreditation');
//        $this->addSql('ALTER TABLE oauth2_access_token CHANGE client client VARCHAR(64) NOT NULL');
//        $this->addSql('ALTER TABLE oauth2_authorization_code CHANGE client client VARCHAR(64) NOT NULL');
//        $this->addSql('ALTER TABLE oauth2_client CHANGE identifier identifier VARCHAR(64) NOT NULL');
//        $this->addSql('ALTER TABLE participant_section CHANGE updated_at updated_at DATETIME DEFAULT NULL');
//        $this->addSql('ALTER TABLE participation CHANGE updated_at updated_at DATETIME DEFAULT NULL');
//        $this->addSql('ALTER TABLE partner CHANGE updated_at updated_at DATETIME DEFAULT NULL');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE section CHANGE updated_at updated_at DATETIME DEFAULT NULL');
//        $this->addSql('DROP INDEX UNIQ_73E858EE5E237E06 ON sector_pic');
//        $this->addSql('DROP INDEX UNIQ_73E858EE989D9B62 ON sector_pic');
//        $this->addSql('DROP INDEX slug_idx ON sector_pic');
    }
}
