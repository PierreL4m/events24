<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230620135507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE event CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('ALTER TABLE job CHANGE time_contract time_contract VARCHAR(255) NOT NULL');
//        $this->addSql('ALTER TABLE key_numbers CHANGE exposants exposants INT NOT NULL, CHANGE offres offres INT NOT NULL, CHANGE candidats candidats INT NOT NULL, CHANGE entretiens entretiens INT NOT NULL');
//        $this->addSql('ALTER TABLE oauth2_access_token CHANGE client client VARCHAR(32) NOT NULL');
//        $this->addSql('ALTER TABLE oauth2_client CHANGE identifier identifier VARCHAR(32) NOT NULL');
//        $this->addSql('ALTER TABLE participation ROW_FORMAT=DYNAMIC');
//        $this->addSql('ALTER TABLE participation ADD pub_mobile VARCHAR(255)');
//        $this->addSql('ALTER TABLE picto_sector_event DROP FOREIGN KEY FK_439AA7AED4DEC6CB');
//        $this->addSql('DROP INDEX IDX_439AA7AED4DEC6CB ON picto_sector_event');
//        $this->addSql('ALTER TABLE picto_sector_event CHANGE picto_sector_id sector_pic_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE picto_sector_event ADD CONSTRAINT FK_439AA7AE198E85FF FOREIGN KEY (sector_pic_id) REFERENCES sector_pic (id)');
//        $this->addSql('CREATE INDEX IDX_439AA7AE198E85FF ON picto_sector_event (sector_pic_id)');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT NOT NULL');
//        $this->addSql('ALTER TABLE section CHANGE updated_at updated_at DATETIME NOT NULL');
//        $this->addSql('DROP INDEX uniq_c5ae918e5e237e06 ON sector_pic');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_73E858EE5E237E06 ON sector_pic (name)');
//        $this->addSql('DROP INDEX uniq_c5ae918e989d9b62 ON sector_pic');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_73E858EE989D9B62 ON sector_pic (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE event CHANGE updated_at updated_at DATETIME DEFAULT NULL');
//        $this->addSql('ALTER TABLE job CHANGE time_contract time_contract VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
//        $this->addSql('ALTER TABLE key_numbers CHANGE exposants exposants INT DEFAULT NULL, CHANGE offres offres INT DEFAULT NULL, CHANGE candidats candidats INT DEFAULT NULL, CHANGE entretiens entretiens INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE oauth2_access_token CHANGE client client VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
//        $this->addSql('ALTER TABLE oauth2_client CHANGE identifier identifier VARCHAR(64) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
//        $this->addSql('ALTER TABLE participation DROP pub_mobile');
//        $this->addSql('ALTER TABLE picto_sector_event DROP FOREIGN KEY FK_439AA7AE198E85FF');
//        $this->addSql('DROP INDEX IDX_439AA7AE198E85FF ON picto_sector_event');
//        $this->addSql('ALTER TABLE picto_sector_event CHANGE sector_pic_id picto_sector_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE picto_sector_event ADD CONSTRAINT FK_439AA7AED4DEC6CB FOREIGN KEY (picto_sector_id) REFERENCES sector_pic (id)');
//        $this->addSql('CREATE INDEX IDX_439AA7AED4DEC6CB ON picto_sector_event (picto_sector_id)');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE section CHANGE updated_at updated_at DATETIME DEFAULT NULL');
//        $this->addSql('DROP INDEX uniq_73e858ee989d9b62 ON sector_pic');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_C5AE918E989D9B62 ON sector_pic (slug)');
//        $this->addSql('DROP INDEX uniq_73e858ee5e237e06 ON sector_pic');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_C5AE918E5E237E06 ON sector_pic (name)');
    }
}
