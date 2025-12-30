<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251230155646 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'oracle', 'Migration can only be executed safely on \'oracle\'.');

        $this->addSql('CREATE SEQUENCE department_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE patient_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE department (id NUMBER(10) NOT NULL, code VARCHAR2(20) NOT NULL, name VARCHAR2(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE patient (id NUMBER(10) NOT NULL, external_id VARCHAR2(50) DEFAULT NULL NULL, date_of_birth DATE NOT NULL, sex VARCHAR2(1) NOT NULL, created_at TIMESTAMP(0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE CHARGE ADD (department_id NUMBER(10) DEFAULT NULL NULL, patient_id NUMBER(10) DEFAULT NULL NULL)');
        $this->addSql('ALTER TABLE CHARGE ADD CONSTRAINT FK_556BA434AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE CHARGE ADD CONSTRAINT FK_556BA4346B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('CREATE INDEX IDX_556BA434AE80F5DF ON CHARGE (department_id)');
        $this->addSql('CREATE INDEX IDX_556BA4346B899279 ON CHARGE (patient_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'oracle', 'Migration can only be executed safely on \'oracle\'.');

        $this->addSql('ALTER TABLE charge DROP CONSTRAINT FK_556BA434AE80F5DF');
        $this->addSql('ALTER TABLE charge DROP CONSTRAINT FK_556BA4346B899279');
        $this->addSql('DROP SEQUENCE department_id_seq');
        $this->addSql('DROP SEQUENCE patient_id_seq');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP INDEX IDX_556BA434AE80F5DF');
        $this->addSql('DROP INDEX IDX_556BA4346B899279');
        $this->addSql('ALTER TABLE charge DROP (department_id, patient_id)');
    }
}
