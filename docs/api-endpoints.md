# API Endpoints Documentation

This document describes the public API endpoints exposed by the **Mini Revenue Integrity & Compliance Analyzer**.
These endpoints are intended for **internal use**, testing, and demonstration purposes and follow REST-style
conventions.

All responses are JSON encoded.

---

## Base URL

```
/api
```

---

## POST /api/charges

### Description

Ingests one or more healthcare charge records into the system.  
Each charge is **normalized, persisted**, and immediately evaluated against all **active rules**.

This endpoint is the primary entry point into the revenue integrity workflow.

---

### Request Body

```json
{
  "charges": [
    {
      "procedure_code": "MRI_BRAIN",
      "department": "RADIOLOGY",
      "charge_amount": 350.00,
      "payer_type": "MEDICARE",
      "service_date": "2025-01-10",
      "diagnosis_codes": ["G93.9"]
    }
  ]
}
```

---

### Field Definitions

| Field | Type | Required | Description |
|-----|------|----------|-------------|
| procedure_code | string | yes | CPT / HCPCS-style procedure identifier |
| department | string | yes | Clinical department responsible for the charge |
| charge_amount | number | yes | Billed amount in dollars |
| payer_type | string | yes | MEDICARE, MEDICAID, COMMERCIAL |
| service_date | string (YYYY-MM-DD) | yes | Date of service |
| diagnosis_codes | array[string] | no | ICD-10 diagnosis codes |

---

### Processing Steps

1. Validate input payload
2. Normalize values (codes, dates, monetary amounts)
3. Persist charge record
4. Evaluate charge against all active rules
5. Generate insights (if applicable)

---

### Response

```json
{
  "status": "accepted",
  "charges_processed": 1,
  "charge_ids": [123]
}
```

---

### Error Responses

| HTTP Status | Description |
|------------|-------------|
| 400 | Invalid request payload |
| 422 | Validation failure |
| 500 | Internal processing error |

---

## GET /api/analysis/{chargeId}

### Description

Returns all **insights, risks, and findings** generated for a specific charge record.

Insights are **explainable**, traceable to rules, and may include estimated financial impact.

---

### URL Parameters

| Parameter | Type | Description |
|----------|------|-------------|
| chargeId | integer | Unique identifier of the charge |

---

### Response Body

```json
{
  "charge_id": 123,
  "insights": [
    {
      "rule_id": "REV_001",
      "type": "revenue",
      "severity": "medium",
      "message": "Charge amount below expected Medicare threshold",
      "revenue_at_risk": 150.00,
      "created_at": "2025-01-10T14:32:00Z"
    }
  ]
}
```

---

### Insight Fields

| Field | Description |
|-----|-------------|
| rule_id | Identifier of the rule that triggered |
| type | revenue, compliance, or data_quality |
| severity | low, medium, high |
| message | Human-readable explanation |
| revenue_at_risk | Dollar amount (nullable) |
| created_at | Timestamp of insight creation |

---

### Error Responses

| HTTP Status | Description |
|------------|-------------|
| 404 | Charge not found |
| 500 | Internal error |

---

## GET /api/charges/{chargeId}

### Description

Returns the **normalized charge record** as stored in the system.

This endpoint is useful for debugging, audits, and verification.

---

### Response

```json
{
  "id": 123,
  "procedure_code": "MRI_BRAIN",
  "department": "RADIOLOGY",
  "charge_amount": 350.00,
  "payer_type": "MEDICARE",
  "service_date": "2025-01-10",
  "diagnosis_codes": ["G93.9"],
  "created_at": "2025-01-10T14:31:12Z"
}
```

---

## API Design Principles

- **Explainability** over raw data
- **Deterministic outputs**
- **Audit-friendly responses**
- **No PHI assumptions**

---

## Notes

- All data is mock or anonymized
- Authentication is intentionally omitted
- Endpoints are versionless for simplicity

---

## Future Endpoints (Optional)

- `GET /api/charges`
- `POST /api/rules`
- `GET /api/rules`
- `POST /api/analyze/batch`

---
