<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024091924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('CREATE TABLE key_numbers (id INT AUTO_INCREMENT NOT NULL, exposants INT NOT NULL, offres INT NOT NULL, candidats INT NOT NULL, entretiens INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('CREATE TABLE oauth2_access_token (identifier CHAR(80) NOT NULL, client VARCHAR(64) NOT NULL, expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_scope)\', revoked TINYINT(1) NOT NULL, INDEX IDX_454D9673C7440455 (client), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('CREATE TABLE oauth2_authorization_code (identifier CHAR(80) NOT NULL, client VARCHAR(64) NOT NULL, expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_scope)\', revoked TINYINT(1) NOT NULL, INDEX IDX_509FEF5FC7440455 (client), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('CREATE TABLE oauth2_client (identifier VARCHAR(64) NOT NULL, name VARCHAR(128) NOT NULL, secret VARCHAR(128) DEFAULT NULL, redirect_uris TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_redirect_uri)\', grants TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_grant)\', scopes TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_scope)\', active TINYINT(1) NOT NULL, allow_plain_text_pkce TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('REPLACE INTO oauth2_client (`identifier`, `name`,`secret`,`redirect_uris`,`grants`,`scopes`,`active`,`allow_plain_text_pkce`) VALUES ("465fd597a66ca51b3cae150ed712db2b", "usekey","561b5d487befb18261992b47887f1e1b122ceb50f0646ae3f7d2f8b5a5e75787d72e2ffd1a931bd428665c339828cae99249dfe7e1cc62108451246af87273d0",NULL,"client_credentials password refresh_token","SUPER_USER",1,0)');
//        $this->addSql('CREATE TABLE oauth2_refresh_token (identifier CHAR(80) NOT NULL, access_token CHAR(80) DEFAULT NULL, expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', revoked TINYINT(1) NOT NULL, INDEX IDX_4DD90732B6A2DD68 (access_token), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F62F1765E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('INSERT INTO region (`name`) VALUES ("Haut-de-France")');
//        $this->addSql('INSERT INTO region (`name`) VALUES ("Normandie")');
//        $this->addSql('INSERT INTO region (`name`) VALUES ("Bretagne")');
//        $this->addSql('INSERT INTO region (`name`) VALUES ("Pays de la Loire")');
//        $this->addSql('INSERT INTO region (`name`) VALUES ("Centre Val de Loire")');
//        $this->addSql('INSERT INTO region (`name`) VALUES ("Champagne-Ardenne")');
//        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D9673C7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE oauth2_authorization_code ADD CONSTRAINT FK_509FEF5FC7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE oauth2_refresh_token ADD CONSTRAINT FK_4DD90732B6A2DD68 FOREIGN KEY (access_token) REFERENCES oauth2_access_token (identifier) ON DELETE SET NULL');
//        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
//        $this->addSql('DROP TABLE access_token');
//        $this->addSql('DROP TABLE auth_code');
//        $this->addSql('DROP TABLE reresh_token');
//        $this->addSql('DROP TABLE client');
//        $this->addSql('ALTER TABLE fbenoit_image RENAME image');
//        $this->addSql('DROP TABLE migration_versions');
//        $this->addSql('ALTER TABLE candidate_participation DROP FOREIGN KEY FK_8523F962BE04EA9');
//        $this->addSql('ALTER TABLE candidate_participation DROP send_at');
//        $this->addSql('ALTER TABLE candidate_participation ADD CONSTRAINT FK_8523F962BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE SET NULL');
//        $this->addSql('DROP INDEX email ON candidates_simple');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76EFCB8B8');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7F98F144A');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA78B2C937');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA710A5B1E5');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7684EC833');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D76957C');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA776F4C1D7');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7E993BBC');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA783FDE077');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7A35D7AF0');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7CDA1D76');
//        $this->addSql('ALTER TABLE event ADD key_numbers_id INT DEFAULT NULL, ADD parent_event_id INT DEFAULT NULL, ADD banner_name VARCHAR(255) DEFAULT NULL, ADD social_logo_name VARCHAR(255) DEFAULT NULL, ADD back_badge_name VARCHAR(255) DEFAULT NULL, ADD pub_name VARCHAR(255) DEFAULT NULL, ADD logo_name VARCHAR(255) DEFAULT NULL, ADD back_facebook_name VARCHAR(255) DEFAULT NULL, ADD back_insta_name VARCHAR(255) DEFAULT NULL, ADD back_twitter_name VARCHAR(255) DEFAULT NULL, ADD back_linkedin_name VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA74A5B5220 FOREIGN KEY (key_numbers_id) REFERENCES key_numbers (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7EE3A445A FOREIGN KEY (parent_event_id) REFERENCES event (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76EFCB8B8 FOREIGN KEY (og_image_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7F98F144A FOREIGN KEY (logo_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA78B2C937 FOREIGN KEY (back_twitter_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA710A5B1E5 FOREIGN KEY (back_insta_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7684EC833 FOREIGN KEY (banner_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D76957C FOREIGN KEY (back_badge_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA776F4C1D7 FOREIGN KEY (social_logo_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7E993BBC FOREIGN KEY (back_facebook_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA783FDE077 FOREIGN KEY (pub_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7A35D7AF0 FOREIGN KEY (invitation_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7CDA1D76 FOREIGN KEY (back_linkedin_id) REFERENCES image (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7569A2F6C ON event (banner_name)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA71554BF9A ON event (social_logo_name)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA734A134D7 ON event (back_badge_name)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7F73AE624 ON event (pub_name)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA72702E2ED ON event (logo_name)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7CE356E99 ON event (back_facebook_name)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7A821FC86 ON event (back_insta_name)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA745F1BA02 ON event (back_twitter_name)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA72D2DD6E3 ON event (back_linkedin_name)');
//        $this->addSql('CREATE INDEX IDX_3BAE0AA74A5B5220 ON event (key_numbers_id)');
//        $this->addSql('CREATE INDEX IDX_3BAE0AA7EE3A445A ON event (parent_event_id)');
//        $this->addSql('ALTER TABLE event_type DROP FOREIGN KEY FK_93151B822EF91FD8');
//        $this->addSql('ALTER TABLE event_type ADD header_name VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE event_type ADD CONSTRAINT FK_93151B822EF91FD8 FOREIGN KEY (header_id) REFERENCES image (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_93151B82F3A7048B ON event_type (header_name)');
//        $this->addSql('UPDATE job SET time_contract = \'\' WHERE time_contract IS NULL;');
//        $this->addSql('ALTER TABLE job CHANGE city_id city_id INT DEFAULT NULL, CHANGE time_contract time_contract VARCHAR(255) NOT NULL');
//        $this->addSql('ALTER TABLE joblink DROP FOREIGN KEY FK_BF12139AF98F144A');
//        $this->addSql('ALTER TABLE joblink ADD CONSTRAINT FK_BF12139AF98F144A FOREIGN KEY (logo_id) REFERENCES image (id)');
//        $this->addSql('DROP INDEX slug_idx ON mobility');
//        $this->addSql('ALTER TABLE participant_section DROP FOREIGN KEY FK_B77EB5E7F98F144A');
//        $this->addSql('ALTER TABLE participant_section ADD CONSTRAINT FK_B77EB5E7F98F144A FOREIGN KEY (logo_id) REFERENCES image (id)');
//        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FF98F144A');
//        $this->addSql('ALTER TABLE participation ADD sector_id INT DEFAULT NULL, ADD logo_name VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FDE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
//        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FF98F144A FOREIGN KEY (logo_id) REFERENCES image (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_AB55E24F2702E2ED ON participation (logo_name)');
//        $this->addSql('CREATE INDEX IDX_AB55E24FDE95C867 ON participation (sector_id)');
//        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E16F98F144A');
//        $this->addSql('ALTER TABLE partner ADD logo_name VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16F98F144A FOREIGN KEY (logo_id) REFERENCES image (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_312B3E162702E2ED ON partner (logo_name)');
//        $this->addSql('ALTER TABLE place ADD region_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
//        $this->addSql('CREATE INDEX IDX_741D53CD98260155 ON place (region_id)');
//        $this->addSql('UPDATE `place` SET `region_id` = 1 WHERE `place`.`city` = "Lomme" OR `place`.`city` = "Valenciennes" OR `place`.`city` = "Dunkerque" OR `place`.`city` = "Amiens" OR `place`.`city` = "Arras (St Laurent Blangy)" OR `place`.`city` = "Boulogne-sur-Mer" OR `place`.`city` = "Saint-Amand-les-Eaux" OR `place`.`city` = "Compiègne" OR `place`.`city` = "Lille" OR `place`.`city` = "Paris" OR `place`.`city` = "BOULOGNE-SUR-MER" OR `place`.`city` = "Valenciennes (anzin)" OR `place`.`city` = "Lille" OR `place`.`city` = "Amiens"');
//        $this->addSql('UPDATE `place` SET `region_id` = 2 WHERE `place`.`city` = "Le Havre" OR `place`.`city` = "Rouen" OR `place`.`city` = "Caen" OR `place`.`city` = "Évreux" OR `place`.`city` = "Cherbourg-en-Cotentin" OR `place`.`city` = "Alençon" OR `place`.`city` = "Rouen (Le Grand-Quevilly)"');
//        $this->addSql('UPDATE `place` SET `region_id` = 3 WHERE `place`.`city` = "Rennes" OR `place`.`city` = "Brest" OR `place`.`city` = "Lorient" OR `place`.`city` = "VANNES" OR `place`.`city` = "QUIMPER"');
//        $this->addSql('UPDATE `place` SET `region_id` = 4 WHERE `place`.`city` = "Nantes" OR `place`.`city` = "NANTES (SAINT-HERBLAIN)" OR `place`.`city` = "ANGERS"');
//        $this->addSql('UPDATE `place` SET `region_id` = 5 WHERE `place`.`city` = "OLIVET" OR `place`.`city` = "LE MANS" OR `place`.`city` = "TOURS"');
//        $this->addSql('UPDATE `place` SET `region_id` = 6 WHERE `place`.`city` = "Reims"');
//        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF3DA5256D');
//        $this->addSql('ALTER TABLE section ADD image_name VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D737AEFAC199498 ON section (image_name)');
//        $this->addSql('DROP INDEX UNIQ_8D93D649C05FB297 ON user');
//        $this->addSql('DROP INDEX UNIQ_8D93D649A0D96FBF ON user');
//        $this->addSql('DROP INDEX UNIQ_8D93D64992FC23A8 ON user');
//        $this->addSql('TRUNCATE TABLE `color`');
//        for ($i = 1; $i <= 35; $i++) {
//            $this->addSql('INSERT INTO color (`place_id`,`name`,`code`) VALUES ('.$i.',"bannière","#FFB51E")');
//            $this->addSql('INSERT INTO color (`place_id`,`name`,`code`) VALUES ('.$i.',"texte principal","#AD5E01")');
//            $this->addSql('INSERT INTO color (`place_id`,`name`,`code`) VALUES ('.$i.',"fond chiffres clés","#596D85")');
//            $this->addSql('INSERT INTO color (`place_id`,`name`,`code`) VALUES ('.$i.',"picto secteur","#CE8B22")');
//            $this->addSql('INSERT INTO color (`place_id`,`name`,`code`) VALUES ('.$i.',"picto chiffres clés","#FFB51E")');
//        }
//        $this->addSql('ALTER TABLE user ADD plain_password VARCHAR(255) DEFAULT NULL, DROP username_canonical, DROP email_canonical, CHANGE username username VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE enabled enabled TINYINT(1) DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL, CHANGE password_requested_at password_requested_at VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7684EC833');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA776F4C1D7');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D76957C');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA783FDE077');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7F98F144A');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7E993BBC');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA710A5B1E5');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA78B2C937');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7CDA1D76');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76EFCB8B8');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7A35D7AF0');
//        $this->addSql('ALTER TABLE event_type DROP FOREIGN KEY FK_93151B822EF91FD8');
//        $this->addSql('ALTER TABLE joblink DROP FOREIGN KEY FK_BF12139AF98F144A');
//        $this->addSql('ALTER TABLE participant_section DROP FOREIGN KEY FK_B77EB5E7F98F144A');
//        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FF98F144A');
//        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E16F98F144A');
//        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF3DA5256D');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA74A5B5220');
//        $this->addSql('ALTER TABLE oauth2_refresh_token DROP FOREIGN KEY FK_4DD90732B6A2DD68');
//        $this->addSql('ALTER TABLE oauth2_access_token DROP FOREIGN KEY FK_454D9673C7440455');
//        $this->addSql('ALTER TABLE oauth2_authorization_code DROP FOREIGN KEY FK_509FEF5FC7440455');
//        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD98260155');
//        $this->addSql('CREATE TABLE access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, expires_at INT DEFAULT NULL, scope VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX UNIQ_B6A2DD685F37A13B (token), INDEX IDX_B6A2DD68A76ED395 (user_id), INDEX IDX_B6A2DD6819EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('CREATE TABLE auth_code (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, redirect_uri LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, expires_at INT DEFAULT NULL, scope VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX UNIQ_5933D02C5F37A13B (token), INDEX IDX_5933D02CA76ED395 (user_id), INDEX IDX_5933D02C19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, redirect_uris LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', secret VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, allowed_grant_types LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\', name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('CREATE TABLE fbenoit_image (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, alt VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, original_path VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, upload_dir VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, updated DATETIME NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, box TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('CREATE TABLE origin (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('CREATE TABLE reresh_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, expires_at INT DEFAULT NULL, scope VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, INDEX IDX_88240AC519EB6921 (client_id), UNIQUE INDEX UNIQ_88240AC55F37A13B (token), INDEX IDX_88240AC5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
//        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
//        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
//        $this->addSql('ALTER TABLE reresh_token ADD CONSTRAINT FK_88240AC5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
//        $this->addSql('ALTER TABLE reresh_token ADD CONSTRAINT FK_88240AC519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
//        $this->addSql('DROP TABLE image');
//        $this->addSql('DROP TABLE key_numbers');
//        $this->addSql('DROP TABLE oauth2_access_token');
//        $this->addSql('DROP TABLE oauth2_authorization_code');
//        $this->addSql('DROP TABLE oauth2_client');
//        $this->addSql('DROP TABLE oauth2_refresh_token');
//        $this->addSql('DROP TABLE region');
//        $this->addSql('DROP TABLE reset_password_request');
//        $this->addSql('ALTER TABLE agenda CHANGE logo_id logo_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC877F98F144A FOREIGN KEY (logo_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CEDC877F98F144A ON agenda (logo_id)');
//        $this->addSql('ALTER TABLE candidate_participation DROP FOREIGN KEY FK_8523F962BE04EA9');
//        $this->addSql('ALTER TABLE candidate_participation ADD send_at DATETIME DEFAULT NULL');
//        $this->addSql('ALTER TABLE candidate_participation ADD CONSTRAINT FK_8523F962BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
//        $this->addSql('CREATE INDEX email ON candidates_simple (email(191))');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7EE3A445A');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7684EC833');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA776F4C1D7');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D76957C');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA783FDE077');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7F98F144A');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7E993BBC');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA710A5B1E5');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA78B2C937');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7CDA1D76');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA76EFCB8B8');
//        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7A35D7AF0');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA7569A2F6C ON event');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA71554BF9A ON event');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA734A134D7 ON event');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA7F73AE624 ON event');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA72702E2ED ON event');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA7CE356E99 ON event');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA7A821FC86 ON event');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA745F1BA02 ON event');
//        $this->addSql('DROP INDEX UNIQ_3BAE0AA72D2DD6E3 ON event');
//        $this->addSql('DROP INDEX IDX_3BAE0AA74A5B5220 ON event');
//        $this->addSql('DROP INDEX IDX_3BAE0AA7EE3A445A ON event');
//        $this->addSql('ALTER TABLE event DROP key_numbers_id, DROP parent_event_id, DROP banner_name, DROP social_logo_name, DROP back_badge_name, DROP pub_name, DROP logo_name, DROP back_facebook_name, DROP back_insta_name, DROP back_twitter_name, DROP back_linkedin_name');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7684EC833 FOREIGN KEY (banner_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA776F4C1D7 FOREIGN KEY (social_logo_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D76957C FOREIGN KEY (back_badge_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA783FDE077 FOREIGN KEY (pub_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7F98F144A FOREIGN KEY (logo_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7E993BBC FOREIGN KEY (back_facebook_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA710A5B1E5 FOREIGN KEY (back_insta_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA78B2C937 FOREIGN KEY (back_twitter_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7CDA1D76 FOREIGN KEY (back_linkedin_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA76EFCB8B8 FOREIGN KEY (og_image_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7A35D7AF0 FOREIGN KEY (invitation_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE event_type DROP FOREIGN KEY FK_93151B822EF91FD8');
//        $this->addSql('DROP INDEX UNIQ_93151B82F3A7048B ON event_type');
//        $this->addSql('ALTER TABLE event_type DROP header_name');
//        $this->addSql('ALTER TABLE event_type ADD CONSTRAINT FK_93151B822EF91FD8 FOREIGN KEY (header_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE job CHANGE city_id city_id INT NOT NULL, CHANGE time_contract time_contract VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
//        $this->addSql('ALTER TABLE joblink DROP FOREIGN KEY FK_BF12139AF98F144A');
//        $this->addSql('ALTER TABLE joblink ADD CONSTRAINT FK_BF12139AF98F144A FOREIGN KEY (logo_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('CREATE INDEX slug_idx ON mobility (slug)');
//        $this->addSql('ALTER TABLE participant_section DROP FOREIGN KEY FK_B77EB5E7F98F144A');
//        $this->addSql('ALTER TABLE participant_section ADD CONSTRAINT FK_B77EB5E7F98F144A FOREIGN KEY (logo_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FDE95C867');
//        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FF98F144A');
//        $this->addSql('DROP INDEX UNIQ_AB55E24F2702E2ED ON participation');
//        $this->addSql('DROP INDEX IDX_AB55E24FDE95C867 ON participation');
//        $this->addSql('ALTER TABLE participation DROP sector_id, DROP logo_name');
//        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FF98F144A FOREIGN KEY (logo_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E16F98F144A');
//        $this->addSql('DROP INDEX UNIQ_312B3E162702E2ED ON partner');
//        $this->addSql('ALTER TABLE partner DROP logo_name');
//        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16F98F144A FOREIGN KEY (logo_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('DROP INDEX IDX_741D53CD98260155 ON place');
//        $this->addSql('ALTER TABLE place DROP region_id');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEF3DA5256D');
//        $this->addSql('DROP INDEX UNIQ_2D737AEFAC199498 ON section');
//        $this->addSql('ALTER TABLE section DROP image_name');
//        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEF3DA5256D FOREIGN KEY (image_id) REFERENCES fbenoit_image (id)');
//        $this->addSql('ALTER TABLE user ADD origin_id INT DEFAULT NULL, ADD username_canonical VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, ADD email_canonical VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, DROP plain_password, CHANGE email email VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE username username VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE enabled enabled TINYINT(1) NOT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
//        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64956A273CC FOREIGN KEY (origin_id) REFERENCES origin (id) ON DELETE SET NULL');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C05FB297 ON user (confirmation_token)');
//        $this->addSql('CREATE INDEX IDX_8D93D64956A273CC ON user (origin_id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A0D96FBF ON user (email_canonical)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64992FC23A8 ON user (username_canonical)');
    }
}
