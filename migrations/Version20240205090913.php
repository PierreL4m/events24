<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240205090913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('CREATE TABLE accreditation (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, participation_id INT DEFAULT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, dtype VARCHAR(255) NOT NULL, INDEX IDX_3BF9D0D871F7E88B (event_id), INDEX IDX_3BF9D0D86ACE3B73 (participation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('ALTER TABLE accreditation ADD CONSTRAINT FK_3BF9D0D871F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE accreditation ADD CONSTRAINT FK_3BF9D0D86ACE3B73 FOREIGN KEY (participation_id) REFERENCES participation (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D936B2FA');
//        $this->addSql('DROP INDEX IDX_3BAE0AA7D936B2FA ON event');
//        $this->addSql('ALTER TABLE event DROP organisateur_id');
//        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE344A0BF17D FOREIGN KEY (candidate_participation_id) REFERENCES candidate_participation (id)');
//        $this->addSql('ALTER TABLE job CHANGE time_contract time_contract VARCHAR(255) NOT NULL');
//        $this->addSql('ALTER TABLE joblink_session_candidate_participation ADD CONSTRAINT FK_67C4A7274A0BF17D FOREIGN KEY (candidate_participation_id) REFERENCES candidate_participation (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE key_numbers CHANGE exposants exposants INT NOT NULL, CHANGE offres offres INT NOT NULL, CHANGE candidats candidats INT NOT NULL, CHANGE entretiens entretiens INT NOT NULL');
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
//        $this->addSql('ALTER TABLE event ADD organisateur_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D936B2FA FOREIGN KEY (organisateur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE SET NULL');
//        $this->addSql('CREATE INDEX IDX_3BAE0AA7D936B2FA ON event (organisateur_id)');
//        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE344A0BF17D');
//        $this->addSql('ALTER TABLE job CHANGE time_contract time_contract VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE joblink_session_candidate_participation DROP FOREIGN KEY FK_67C4A7274A0BF17D');
//        $this->addSql('ALTER TABLE key_numbers CHANGE exposants exposants INT DEFAULT NULL, CHANGE offres offres INT DEFAULT NULL, CHANGE candidats candidats INT DEFAULT NULL, CHANGE entretiens entretiens INT DEFAULT NULL');
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
