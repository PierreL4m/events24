<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019090214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F62F1765E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
//        $this->addSql('ALTER TABLE place ADD region_id INT DEFAULT NULL');
//        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
//        $this->addSql('CREATE INDEX IDX_741D53CD98260155 ON place (region_id)');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD98260155');
//        $this->addSql('DROP TABLE region');
//        $this->addSql('DROP INDEX IDX_741D53CD98260155 ON place');
//        $this->addSql('ALTER TABLE place DROP region_id');
//        $this->addSql('ALTER TABLE recall_subscribe CHANGE event_id event_id INT DEFAULT NULL');
    }
}
