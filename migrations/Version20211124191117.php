<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211124191117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX strProductCode ON tblproductdata');
        $this->addSql('ALTER TABLE tblproductdata CHANGE intProductDataId intProductDataId INT AUTO_INCREMENT NOT NULL, CHANGE dtmAdded dtmAdded DATETIME NOT NULL, CHANGE dtmDiscontinued dtmDiscontinued DATETIME NOT NULL, CHANGE stmTimestamp stmTimestamp DATETIME DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tblProductData CHANGE intProductDataId intProductDataId INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE dtmAdded dtmAdded DATETIME DEFAULT NULL, CHANGE dtmDiscontinued dtmDiscontinued DATETIME DEFAULT NULL, CHANGE stmTimestamp stmTimestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX strProductCode ON tblProductData (strProductCode)');
    }
}
