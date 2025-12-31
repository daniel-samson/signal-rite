<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251231144259 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'oracle', 'Migration can only be executed safely on \'oracle\'.');

        $this->addSql('CREATE SEQUENCE diagnoses_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE charge_diagnosis (charge_id NUMBER(10) NOT NULL, diagnosis_id NUMBER(10) NOT NULL, PRIMARY KEY(charge_id, diagnosis_id))');
        $this->addSql('CREATE INDEX IDX_1771257F55284914 ON charge_diagnosis (charge_id)');
        $this->addSql('CREATE INDEX IDX_1771257F3CBE4D00 ON charge_diagnosis (diagnosis_id)');
        $this->addSql('CREATE TABLE diagnoses (id NUMBER(10) NOT NULL, code VARCHAR2(20) NOT NULL, description VARCHAR2(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE charge_diagnosis ADD CONSTRAINT FK_1771257F55284914 FOREIGN KEY (charge_id) REFERENCES charge (id)');
        $this->addSql('ALTER TABLE charge_diagnosis ADD CONSTRAINT FK_1771257F3CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnoses (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'oracle', 'Migration can only be executed safely on \'oracle\'.');

        $this->addSql('ALTER TABLE charge_diagnosis DROP CONSTRAINT FK_1771257F3CBE4D00');
        $this->addSql('DROP SEQUENCE diagnoses_id_seq');
        $this->addSql('DROP TABLE charge_diagnosis');
        $this->addSql('DROP TABLE diagnoses');
    }
}
