<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821120616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('CREATE TABLE events_sector_pic (event_id INT NOT NULL, sector_pic_id INT NOT NULL, INDEX IDX_A611ED6F71F7E88B (event_id), INDEX IDX_A611ED6F198E85FF (sector_pic_id), PRIMARY KEY(event_id, sector_pic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('CREATE TABLE event_jobs_sector_pic (event_jobs_id INT NOT NULL, sector_pic_id INT NOT NULL, INDEX IDX_A7274196CD27EF88 (event_jobs_id), INDEX IDX_A7274196198E85FF (sector_pic_id), PRIMARY KEY(event_jobs_id, sector_pic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('CREATE TABLE sector_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5AB028C85E237E06 (name), UNIQUE INDEX UNIQ_5AB028C8989D9B62 (slug), INDEX slug_idx (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('ALTER TABLE events_sector_pic ADD CONSTRAINT FK_A611ED6F71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE events_sector_pic ADD CONSTRAINT FK_A611ED6F198E85FF FOREIGN KEY (sector_pic_id) REFERENCES sector_pic (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE event_jobs_sector_pic ADD CONSTRAINT FK_A7274196CD27EF88 FOREIGN KEY (event_jobs_id) REFERENCES event (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE event_jobs_sector_pic ADD CONSTRAINT FK_A7274196198E85FF FOREIGN KEY (sector_pic_id) REFERENCES sector_pic (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE event CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('ALTER TABLE job CHANGE time_contract time_contract VARCHAR(255) NOT NULL');
//        $this->addSql('ALTER TABLE key_numbers CHANGE exposants exposants INT NOT NULL, CHANGE offres offres INT NOT NULL, CHANGE candidats candidats INT NOT NULL, CHANGE entretiens entretiens INT NOT NULL');
//        $this->addSql('ALTER TABLE oauth2_access_token CHANGE client client VARCHAR(32) NOT NULL');
//        $this->addSql('ALTER TABLE oauth2_client CHANGE identifier identifier VARCHAR(32) NOT NULL');
//        $this->addSql('ALTER TABLE participation CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT NOT NULL');
//        $this->addSql('ALTER TABLE section CHANGE updated_at updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('DROP TABLE events_sector_pic');
//        $this->addSql('DROP TABLE event_jobs_sector_pic');
//        $this->addSql('DROP TABLE sector_type');
//        $this->addSql('ALTER TABLE event CHANGE updated_at updated_at DATETIME DEFAULT NULL');
//        $this->addSql('ALTER TABLE job CHANGE time_contract time_contract VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
//        $this->addSql('ALTER TABLE key_numbers CHANGE exposants exposants INT DEFAULT NULL, CHANGE offres offres INT DEFAULT NULL, CHANGE candidats candidats INT DEFAULT NULL, CHANGE entretiens entretiens INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE oauth2_access_token CHANGE client client VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
//        $this->addSql('ALTER TABLE oauth2_client CHANGE identifier identifier VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
//        $this->addSql('ALTER TABLE participation CHANGE updated_at updated_at DATETIME DEFAULT NULL');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE section CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }
}
