<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128125405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64956A273CC');
//        $this->addSql('DROP TABLE origin');
//        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC877F98F144A');
//        $this->addSql('DROP INDEX UNIQ_2CEDC877F98F144A ON agenda');
//        $this->addSql('ALTER TABLE agenda CHANGE logo_id logo_id VARCHAR(255) DEFAULT NULL');
//        $this->addSql('ALTER TABLE candidate_participation DROP send_at');
//        $this->addSql('ALTER TABLE event ADD updated_at DATETIME');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT NOT NULL');
//        $this->addSql('DROP INDEX IDX_8D93D64956A273CC ON user');
//        $this->addSql('ALTER TABLE user DROP origin_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('CREATE TABLE origin (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('ALTER TABLE agenda CHANGE logo_id logo_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC877F98F144A FOREIGN KEY (logo_id) REFERENCES image (id)');
//        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CEDC877F98F144A ON agenda (logo_id)');
//        $this->addSql('ALTER TABLE candidate_participation ADD send_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE event DROP updated_at');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE user ADD origin_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64956A273CC FOREIGN KEY (origin_id) REFERENCES origin (id) ON DELETE SET NULL');
//        $this->addSql('CREATE INDEX IDX_8D93D64956A273CC ON user (origin_id)');
    }
}
