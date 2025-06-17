<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220154218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE accreditation ADD accreditation_path VARCHAR(255) DEFAULT NULL');
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
//        $this->addSql('ALTER TABLE accreditation DROP accreditation_path');
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
