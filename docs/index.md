# SignalRite
 Mini Revenue Integrity & Compliance Analyzer

## Purpose

This project is a **small, focused backend service** that simulates how healthcare financial systems identify **revenue leakage, compliance risk, and billing anomalies** using **rules-driven analysis** and **explainable outputs**.

The goal is **not** to recreate a full billing system, but to demonstrate:
- How healthcare charge data is evaluated
- How rules and compliance logic are applied
- How actionable financial insights are produced

This project is designed to be built with:
- **Symfony 3**
- **PHP 7.2**
- **Doctrine ORM**
- **Oracle Database**

---

## High-Level Architecture

```
+---------------------------+
| Controller (API / Web)    |
+---------------------------+
            |
            V
+---------------------------+        +----------------------+
|     Persist Charge        |        | ChargeRepository     |
|  (Normalization on save)  |        | PatientRepository    |
+---------------------------+        | DepartmentRepository |
            |                        | DiagnosisRepository  |
            V                        +----------------------+
+---------------------------+
|    ChargeCreatedEvent     |
+---------------------------+
            |
            V
+---------------------------+
|   [ Event Listener ]      |
+---------------------------+
            |
            V
+---------------------------+
| Queue Job:                |
|   AnalyzeChargeJob        |
+---------------------------+
            |
            V
+---------------------------+        +----------------------+
|  RiskAnalyzerService      |        | RuleRepository       |
|  - Load active rules      |------->| (findBy active=true) |
|  - Build context          |        +----------------------+
|  - Evaluate conditions    |
|  - Generate insights      |        +----------------------+
+---------------------------+        | InsightRepository    |
            |                        | (persist new)        |
            V                        +----------------------+
+---------------------------+
| InsightsGeneratedEvent    |
| (only if insights exist)  |
+---------------------------+
            |
            V
+---------------------------+
|   [ Event Listener ]      |
+---------------------------+
            |
            V
+---------------------------+
| Notification / Email      |
+---------------------------+
```

---

## Core Concepts

### 1. Charge Records

A **charge record** represents a single billable healthcare event.

Fields:
- `procedure_codes` - **CPT/HCPCS** billing codes (many-to-many, e.g., ["99213", "99213-25", "70553-TC"])
- `diagnosis_codes` - **ICD-10** diagnosis codes (many-to-many, e.g., ["I10", "E11.9"])
- `charge_amount_cents` - Amount in cents (e.g., 50000 = $500.00)
- `payer_type` - MEDICARE, MEDICAID, COMMERCIAL, SELF_PAY
- `service_date` - Date of service
- `department` - Department that generated the charge
- `patient` - Associated patient record

Codes with modifiers are stored as separate entries (e.g., "99213" and "99213-25" are distinct ProcedureCode records).

Normalization happens automatically on save:
- Uppercase codes
- Monetary values stored as cents
- Canonical payer types (via enum)
- Immutable dates

---

### 2. Rules Engine

Rules are defined in YAML using [nicoSWD/php-rule-parser](https://github.com/nicoSWD/php-rule-parser) syntax.

Example:

```yaml
- id: REV_001
  name: MRI Undercharge Detection
  type: revenue
  description: Charge amount below expected threshold
  severity: medium
  condition: >
    procedure_code.startsWith("70") &&
    payer_type == "MEDICARE" &&
    charge_amount_cents < 50000
  message: "MRI charge below Medicare minimum threshold"
  tags: [imaging, medicare, undercharge]
```

See [Rules Engine Documentation](rules-engine.md) for full schema, operators, and examples.

---

### 3. Event-Driven Analysis Flow

The analysis pipeline uses events and queues for decoupled, scalable processing:

1. **ChargeCreatedEvent** - Dispatched after a Charge is persisted
2. **AnalyzeChargeJob** - Queued job that runs asynchronously
3. **RiskAnalyzerService** - Evaluates charge against active rules
4. **InsightsGeneratedEvent** - Dispatched only if insights were generated
5. **Notification/Email** - Alerts stakeholders of findings

### 4. Risk Analyzer Service

The core analysis service that:
1. Loads active rules from the database (RuleRepository)
2. Builds evaluation context from Charge entity (toRuleContext)
3. Evaluates each rule condition using php-rule-parser
4. Creates Insight entities for matched rules
5. Returns insights for the InsightsCreatedEvent dispatch

---

## API Endpoints

### POST /charges

Ingest charge data.

### GET /analysis/{chargeId}

Returns insights for a charge.

---

## Design Principles

- Explainability
- Configuration over code
- Deterministic results
- Audit-friendly

---

## Non-Goals

- No real CPT pricing
- No PHI
- No claims submission

---

## Success Criteria

- Ingest data
- Apply rules
- Detect risk
- Explain findings
- Estimate impact

**Next:** [Database ERD](database-erd.md) | [Rules Engine](rules-engine.md)