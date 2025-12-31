# Database Entity Relationship Diagram

## Overview

SignalRite uses Oracle Database with Doctrine ORM. This document shows the entity relationships.

---

## Entity Relationship Diagram

```
                                    +------------------+
                                    |    DEPARTMENT    |
                                    +------------------+
                                    | id          (PK) |
                                    | code             |
                                    | name             |
                                    +--------+---------+
                                             |
                                             | 1
                                             |
                                             | *
+------------------+                +--------+---------+                +------------------+
|     PATIENT      |                |      CHARGE      |                |       RULE       |
+------------------+                +------------------+                +------------------+
| id          (PK) |                | id          (PK) |                | id          (PK) |
| external_id      | 1            * | procedure_code   |                | type             |
| date_of_birth    +----------------+ charge_amount_   |                | description      |
| sex              |                |    cents         |                | definition_yaml  |
+------------------+                | payer_type       |                | active           |
                                    | service_date     |                | created_at       |
                                    | created_at       |                +--------+---------+
                                    | department_id(FK)|                         |
                                    | patient_id  (FK) |                         | 1
                                    +--------+---------+                         |
                                             |                                   |
                         +-------------------+-------------------+               |
                         |                                       |               |
                         | *                                     | *             | *
                +--------+---------+                    +--------+---------+-----+
                | CHARGE_DIAGNOSIS |                    |      INSIGHT     |
                +------------------+                    +------------------+
                | charge_id   (FK) |                    | id          (PK) |
                | diagnosis_id(FK) |                    | severity         |
                +--------+---------+                    | message          |
                         | *                            | revenue_at_risk_ |
                         |                              |    in_cents      |
                +--------+---------+                    | created_at       |
                |    DIAGNOSIS     |                    | charge_id   (FK) |
                +------------------+                    | rule_id     (FK) |
                | id          (PK) |                    +------------------+
                | code             |
                | description      |
                +------------------+
```

---

## Relationships Summary

| Relationship | Type | Description |
|-------------|------|-------------|
| Patient -> Charge | 1:N | A patient can have many charges |
| Department -> Charge | 1:N | A department can have many charges |
| Charge <-> Diagnosis | M:N | A charge can have multiple diagnoses; a diagnosis can appear on multiple charges |
| Charge -> Insight | 1:N | A charge can trigger multiple insights |
| Rule -> Insight | 1:N | A rule can generate multiple insights |

---

## Entity Details

### PATIENT

Healthcare patient entity storing demographic information used for eligibility checks and rule validation. A patient can have multiple charges associated with them.

| Column | Type | Description |
|--------|------|-------------|
| id | NUMBER(10) | Primary key |
| external_id | VARCHAR2(100) | External system identifier (e.g., MRN, EHR ID) |
| date_of_birth | DATE | Patient date of birth, used for age-based rule validation |
| sex | VARCHAR2(1) | Patient sex (M, F, or O) for gender-specific procedure validation |

### DEPARTMENT

Organizational unit representing a hospital or clinic department (e.g., Cardiology, Orthopedics). Departments generate charges and are used for departmental reporting and rule-based filtering.

| Column | Type | Description |
|--------|------|-------------|
| id | NUMBER(10) | Primary key |
| code | VARCHAR2(20) | Short department code (e.g., CARD, ORTH, EMER) |
| name | VARCHAR2(100) | Full department name (e.g., Cardiology, Orthopedics) |

### CHARGE

Central entity representing a single billable healthcare event. Captures procedure codes, amounts, payer information, and links to patient, department, and diagnosis records. The Rules Engine evaluates charges to generate Insights.

| Column | Type | Description |
|--------|------|-------------|
| id | NUMBER(10) | Primary key |
| procedure_code | VARCHAR2(50) | CPT/HCPCS code for procedures, services, supplies |
| charge_amount_cents | NUMBER(10) | Charge amount stored in cents for precision |
| payer_type | VARCHAR2(20) | Payer category: MEDICARE, MEDICAID, COMMERCIAL |
| service_date | DATE | Date of service for the billable event |
| created_at | TIMESTAMP | Record creation timestamp |
| department_id | NUMBER(10) | FK to department that generated this charge |
| patient_id | NUMBER(10) | FK to patient associated with this charge |

### DIAGNOSIS

ICD-10 diagnosis code entity establishing medical necessity for procedures. A charge can have multiple diagnoses, and each diagnosis can appear on multiple charges (many-to-many). The Rules Engine validates diagnosis-procedure relationships.

| Column | Type | Description |
|--------|------|-------------|
| id | NUMBER(10) | Primary key |
| code | VARCHAR2(20) | ICD-10 diagnosis code (e.g., E11.9, I10, J06.9) |
| description | VARCHAR2(255) | Human-readable diagnosis description |

### CHARGE_DIAGNOSIS (Join Table)

Many-to-many relationship table linking charges to their supporting diagnosis codes.

| Column | Type | Description |
|--------|------|-------------|
| charge_id | NUMBER(10) | FK to charge |
| diagnosis_id | NUMBER(10) | FK to diagnosis |

### RULE

Compliance or validation rule entity. Rules are configuration-driven and stored as YAML definitions. The Rules Engine evaluates each charge against active rules and generates Insights when conditions match. Rules are persisted for auditability and deterministic evaluation.

| Column | Type | Description |
|--------|------|-------------|
| id | NUMBER(10) | Primary key |
| type | VARCHAR2(20) | Rule category: ELIGIBILITY, PRICING, VALIDATION, AUTHORIZATION |
| description | VARCHAR2(255) | Human-readable description explaining what the rule checks |
| definition_yaml | CLOB | YAML rule definition containing conditions, thresholds, and severity |
| active | NUMBER(1) | Whether rule is active (1) or inactive (0) for audit retention |
| created_at | TIMESTAMP | Record creation timestamp |

### INSIGHT

Rule-generated finding entity. Insights are the output of the Rules Engine. When a charge matches a rule's conditions, an insight is created with severity, message, and optional revenue impact. Insights provide explainable, actionable findings for revenue integrity.

| Column | Type | Description |
|--------|------|-------------|
| id | NUMBER(10) | Primary key |
| severity | VARCHAR2(20) | Urgency level: LOW, MEDIUM, HIGH, CRITICAL |
| message | VARCHAR2(500) | Human-readable message explaining the finding |
| revenue_at_risk_in_cents | NUMBER(10) | Estimated revenue impact for prioritization |
| created_at | TIMESTAMP | Record creation timestamp |
| charge_id | NUMBER(10) | FK to the charge that triggered this insight |
| rule_id | NUMBER(10) | FK to the rule that generated this insight |

---

## Data Flow

```
+----------+     +------------+     +--------+     +---------+
| Patient  | --> |   Charge   | --> |  Rule  | --> | Insight |
+----------+     +-----+------+     | Engine |     +---------+
                       |            +--------+
+------------+         |
| Department | --------+
+------------+         |
                       |
+------------+         |
| Diagnosis  | --------+
+------------+   (M:N)
```

1. **Charge** is created with patient, department, and diagnosis associations
2. **Rules Engine** evaluates each charge against active rules
3. **Insights** are generated when rules match, linked to both charge and rule
