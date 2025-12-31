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
Charge Data Input (API / Manuel web interface)
        |
        V
Normalization Layer
        |
        V
Rules Engine
        |
        V
Risk Analyzer
        |
        V
Insight Output (JSON / API)
```

---

## Core Concepts

### 1. Charge Records

A **charge record** represents a single billable healthcare event.

Fields:
- procedure_code
- department
- charge_amount
- payer_type
- service_date
- diagnosis_codes

---

### 2. Normalization Layer

Ensures predictable input:
- Uppercase codes
- Monetary values stored as cents
- Canonical payer types
- Immutable dates

---

### 3. Rules Engine

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
    charge_amount < 50000
  message: "MRI charge below Medicare minimum threshold"
  tags: [imaging, medicare, undercharge]
```

See [Rules Engine Documentation](rules-engine.md) for full schema, operators, and examples.

---

### 4. Risk Analyzer

Aggregates rule matches into explainable insights with optional revenue impact.

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