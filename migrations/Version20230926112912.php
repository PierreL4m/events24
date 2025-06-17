<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230926112912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE344A0BF17D');
//        $this->addSql('ALTER TABLE joblink_session_candidate_participation DROP FOREIGN KEY FK_67C4A7274A0BF17D');
//        $this->addSql('CREATE TABLE sector_pic_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_EB45D2EC5E237E06 (name), UNIQUE INDEX UNIQ_EB45D2EC989D9B62 (slug), INDEX slug_idx (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('ALTER TABLE event_jobs_sector_pic DROP FOREIGN KEY FK_A7274196198E85FF');
//        $this->addSql('ALTER TABLE event_jobs_sector_pic DROP FOREIGN KEY FK_A7274196CD27EF88');
//        $this->addSql('DROP TABLE event_jobs_sector_pic');
//        $this->addSql('DROP TABLE ko_old_candidate_participation');
//        $this->addSql('DROP TABLE sector_pic_event_jobs');
//        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC877F98F144A');
//        $this->addSql('DROP INDEX UNIQ_2CEDC877F98F144A ON agenda');
//        $this->addSql('ALTER TABLE agenda CHANGE logo_id logo_id VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE event CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('ALTER TABLE events_sector_pic ADD CONSTRAINT FK_A611ED6F71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE events_sector_pic ADD CONSTRAINT FK_A611ED6F198E85FF FOREIGN KEY (sector_pic_id) REFERENCES sector_pic (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE344A0BF17D');
//        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE344A0BF17D FOREIGN KEY (candidate_participation_id) REFERENCES candidate_participation (id)');
//        $this->addSql('ALTER TABLE job CHANGE time_contract time_contract VARCHAR(255) NOT NULL');
//        $this->addSql('ALTER TABLE joblink_session_candidate_participation DROP FOREIGN KEY FK_67C4A7274A0BF17D');
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
//        $this->addSql('CREATE TABLE event_jobs_sector_pic (event_jobs_id INT NOT NULL, sector_pic_id INT NOT NULL, INDEX FK_A7274196198E85FF (sector_pic_id), INDEX FK_A7274196CD27EF88 (event_jobs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('CREATE TABLE ko_old_candidate_participation (id INT AUTO_INCREMENT NOT NULL, status_id INT DEFAULT NULL, heard_from_id INT DEFAULT NULL, partner_id INT DEFAULT NULL, game_session_id INT DEFAULT NULL, event_id INT DEFAULT NULL, job_id INT DEFAULT NULL, candidate_id INT DEFAULT NULL, invitation_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, qr_code VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, scanned_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, comes_from VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, rh_comment LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, handled_by_id INT DEFAULT NULL, status_date DATETIME NOT NULL, slot_id INT DEFAULT NULL, send_at DATETIME DEFAULT NULL, INDEX IDX_8523F9626BF700BD (status_id), INDEX IDX_8523F962E0B87F43 (heard_from_id), INDEX IDX_8523F9629393F8FE (partner_id), INDEX IDX_8523F9628FE32B32 (game_session_id), INDEX IDX_8523F96271F7E88B (event_id), INDEX IDX_8523F96291BD8781 (candidate_id), INDEX IDX_8523F962FE65AF40 (handled_by_id), INDEX IDX_8523F962BE04EA9 (job_id), INDEX IDX_8523F96259E5119C (slot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('CREATE TABLE sector_pic_event_jobs (id INT AUTO_INCREMENT NOT NULL, sector_pic_id INT NOT NULL, event_jobs_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('ALTER TABLE event_jobs_sector_pic ADD CONSTRAINT FK_A7274196198E85FF FOREIGN KEY (sector_pic_id) REFERENCES sector_pic (id) ON UPDATE NO ACTION ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE event_jobs_sector_pic ADD CONSTRAINT FK_A7274196CD27EF88 FOREIGN KEY (event_jobs_id) REFERENCES event (id) ON UPDATE NO ACTION ON DELETE CASCADE');
//        $this->addSql('DROP TABLE sector_pic_type');
//        $this->addSql('ALTER TABLE agenda CHANGE logo_id logo_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC877F98F144A FOREIGN KEY (logo_id) REFERENCES image (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CEDC877F98F144A ON agenda (logo_id)');
//        $this->addSql('ALTER TABLE event CHANGE updated_at updated_at DATETIME DEFAULT NULL');
//        $this->addSql('ALTER TABLE events_sector_pic DROP FOREIGN KEY FK_A611ED6F71F7E88B');
//        $this->addSql('ALTER TABLE events_sector_pic DROP FOREIGN KEY FK_A611ED6F198E85FF');
//        $this->addSql('ALTER TABLE grade DROP FOREIGN KEY FK_595AAE344A0BF17D');
//        $this->addSql('ALTER TABLE grade ADD CONSTRAINT FK_595AAE344A0BF17D FOREIGN KEY (candidate_participation_id) REFERENCES ko_old_candidate_participation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
//        $this->addSql('ALTER TABLE job CHANGE time_contract time_contract VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE joblink_session_candidate_participation DROP FOREIGN KEY FK_67C4A7274A0BF17D');
//        $this->addSql('ALTER TABLE joblink_session_candidate_participation ADD CONSTRAINT FK_67C4A7274A0BF17D FOREIGN KEY (candidate_participation_id) REFERENCES ko_old_candidate_participation (id) ON UPDATE NO ACTION ON DELETE CASCADE');
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
