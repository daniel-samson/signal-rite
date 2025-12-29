<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229100315 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'oracle', 'Migration can only be executed safely on \'oracle\'.');

        $this->addSql('CREATE SEQUENCE charge_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE insight_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE rule_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE charge (id NUMBER(10) NOT NULL, procedure_code VARCHAR2(50) NOT NULL, charge_amount_cents NUMBER(10) NOT NULL, payer_type VARCHAR2(20) NOT NULL, service_date DATE NOT NULL, created_at TIMESTAMP(0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE insight (id NUMBER(10) NOT NULL, charge_id NUMBER(10) DEFAULT NULL NULL, rule_id NUMBER(10) DEFAULT NULL NULL, severity VARCHAR2(10) NOT NULL, message VARCHAR2(255) NOT NULL, revenue_at_risk_in_cents VARCHAR2(255) NOT NULL, created_at TIMESTAMP(0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FE3413DB55284914 ON insight (charge_id)');
        $this->addSql('CREATE INDEX IDX_FE3413DB744E0351 ON insight (rule_id)');
        $this->addSql('CREATE TABLE rule (id NUMBER(10) NOT NULL, type VARCHAR2(20) NOT NULL, description VARCHAR2(255) NOT NULL, definition_yaml CLOB NOT NULL, active NUMBER(1) NOT NULL, created_at TIMESTAMP(0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE insight ADD CONSTRAINT FK_FE3413DB55284914 FOREIGN KEY (charge_id) REFERENCES charge (id)');
        $this->addSql('ALTER TABLE insight ADD CONSTRAINT FK_FE3413DB744E0351 FOREIGN KEY (rule_id) REFERENCES rule (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'oracle', 'Migration can only be executed safely on \'oracle\'.');

        $this->addSql('ALTER TABLE insight DROP CONSTRAINT FK_FE3413DB55284914');
        $this->addSql('ALTER TABLE insight DROP CONSTRAINT FK_FE3413DB744E0351');
        $this->addSql('DROP SEQUENCE charge_id_seq');
        $this->addSql('DROP SEQUENCE insight_id_seq');
        $this->addSql('DROP SEQUENCE rule_id_seq');
        $this->addSql('DROP TABLE charge');
        $this->addSql('DROP TABLE insight');
        $this->addSql('DROP TABLE rule');
    }
}
