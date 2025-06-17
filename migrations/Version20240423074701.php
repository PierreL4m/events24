<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240423074701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE accreditation DROP FOREIGN KEY FK_3BF9D0D883FDE077');
//        $this->addSql('DROP INDEX UNIQ_3BF9D0D883FDE077 ON accreditation');
//        $this->addSql('DROP INDEX UNIQ_3BF9D0D8F73AE624 ON accreditation');
//        $this->addSql('ALTER TABLE accreditation DROP pub_id, DROP pub_name');
//        $this->addSql('ALTER TABLE event ADD pub_accred_id INT DEFAULT NULL, ADD pub_accred_name VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7DC907298 FOREIGN KEY (pub_accred_id) REFERENCES image (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA730DDA402 ON event (pub_accred_name)');
//        $this->addSql('CREATE INDEX IDX_3BAE0AA7DC907298 ON event (pub_accred_id)');
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
//        $this->addSql('ALTER TABLE accreditation ADD pub_id INT DEFAULT NULL, ADD pub_name VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE accreditation ADD CONSTRAINT FK_3BF9D0D883FDE077 FOREIGN KEY (pub_id) REFERENCES image (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BF9D0D883FDE077 ON accreditation (pub_id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BF9D0D8F73AE624 ON accreditation (pub_name)');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7DC907298');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA730DDA402 ON event');
//        $this->addSql('DROP INDEX IDX_3BAE0AA7DC907298 ON event');
//        $this->addSql('ALTER TABLE event DROP pub_accred_id, DROP pub_accred_name, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
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
