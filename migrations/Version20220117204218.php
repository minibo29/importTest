<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220117204218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, priority_id INT DEFAULT NULL, type_id INT DEFAULT NULL, status_id INT NOT NULL, strTaskName VARCHAR(50) NOT NULL, strTaskDesc VARCHAR(255) NOT NULL, dtmScheduleTime DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_527EDB25497B19F9 (priority_id), UNIQUE INDEX UNIQ_527EDB25C54C8C93 (type_id), INDEX IDX_527EDB256BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_task (task_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9DF7952B8DB60186 (task_id), UNIQUE INDEX UNIQ_9DF7952BA76ED395 (user_id), PRIMARY KEY(task_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_priority (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, ins INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_task (id INT AUTO_INCREMENT NOT NULL, user INT NOT NULL, task INT NOT NULL, user_role INT NOT NULL, INDEX IDX_28FF97EC8D93D649 (user), INDEX IDX_28FF97EC527EDB25 (task), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25497B19F9 FOREIGN KEY (priority_id) REFERENCES task_priority (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25C54C8C93 FOREIGN KEY (type_id) REFERENCES task_type (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB256BF700BD FOREIGN KEY (status_id) REFERENCES task_status (id)');
        $this->addSql('ALTER TABLE users_task ADD CONSTRAINT FK_9DF7952B8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE users_task ADD CONSTRAINT FK_9DF7952BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_task ADD CONSTRAINT FK_28FF97EC8D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_task ADD CONSTRAINT FK_28FF97EC527EDB25 FOREIGN KEY (task) REFERENCES task (id)');
        $this->addSql('ALTER TABLE tblProductData CHANGE stmTimestamp stmTimestamp DATETIME DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users_task DROP FOREIGN KEY FK_9DF7952B8DB60186');
        $this->addSql('ALTER TABLE user_task DROP FOREIGN KEY FK_28FF97EC527EDB25');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25497B19F9');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB256BF700BD');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25C54C8C93');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE users_task');
        $this->addSql('DROP TABLE task_priority');
        $this->addSql('DROP TABLE task_status');
        $this->addSql('DROP TABLE task_type');
        $this->addSql('DROP TABLE user_task');
        $this->addSql('ALTER TABLE tblProductData CHANGE stmTimestamp stmTimestamp DATETIME DEFAULT CURRENT_TIMESTAMP');
    }
}
