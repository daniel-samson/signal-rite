# Rules Engine

The SignalRite rules engine uses [nicoSWD/php-rule-parser](https://github.com/nicoSWD/php-rule-parser) to evaluate charge data against configurable compliance and revenue rules.

## Installation

```bash
composer require nicoswd/php-rule-parser
```

---

## YAML Schema

Rules are defined in YAML format and stored in `config/rules/`.

### Schema Definition

```yaml
rules:
  - id: string          # Unique rule identifier (required)
    name: string        # Human-readable name (required)
    type: string        # Rule category: revenue|compliance|audit (required)
    description: string # Detailed explanation (required)
    severity: string    # Risk level: low|medium|high|critical (required)
    condition: string   # Expression using php-rule-parser syntax (required)
    message: string     # Output message when rule triggers (required)
    tags: [string]      # Optional categorization tags
    enabled: boolean    # Optional, defaults to true
```

### Variable Context

When rules are evaluated, the following variables are available from the Charge entity:

| Variable | Type | Description |
|----------|------|-------------|
| `procedure_code` | string | CPT/HCPCS procedure code |
| `charge_amount` | int | Amount in cents |
| `payer_type` | string | MEDICARE, MEDICAID, COMMERCIAL, SELF_PAY |
| `service_date` | string | ISO date (YYYY-MM-DD) |
| `department_code` | string | Department identifier |
| `diagnosis_codes` | array | Array of ICD-10 diagnosis codes |
| `patient_type` | string | INPATIENT, OUTPATIENT, EMERGENCY |

---

## Operators

### Comparison Operators

| Operator | Description | Example |
|----------|-------------|---------|
| `==` | Equal (loose) | `payer_type == "MEDICARE"` |
| `===` | Equal (strict) | `charge_amount === 50000` |
| `!=` | Not equal (loose) | `payer_type != "SELF_PAY"` |
| `!==` | Not equal (strict) | `charge_amount !== 0` |
| `>` | Greater than | `charge_amount > 100000` |
| `>=` | Greater than or equal | `charge_amount >= 50000` |
| `<` | Less than | `charge_amount < 1000` |
| `<=` | Less than or equal | `charge_amount <= 500000` |

### Logical Operators

| Operator | Description | Example |
|----------|-------------|---------|
| `&&` | Logical AND | `payer_type == "MEDICARE" && charge_amount > 50000` |
| `\|\|` | Logical OR | `payer_type == "MEDICAID" \|\| payer_type == "MEDICARE"` |

### Containment Operators

| Operator | Description | Example |
|----------|-------------|---------|
| `in` | Value exists in array | `procedure_code in ["99213", "99214", "99215"]` |
| `not in` | Value not in array | `payer_type not in ["SELF_PAY", "CHARITY"]` |

---

## Built-in Methods

### String Methods

| Method | Description | Example |
|--------|-------------|---------|
| `startsWith(str)` | Check prefix | `procedure_code.startsWith("99")` |
| `endsWith(str)` | Check suffix | `procedure_code.endsWith("T")` |
| `indexOf(str)` | Find position (-1 if not found) | `procedure_code.indexOf("MRI") >= 0` |
| `toUpperCase()` | Convert to uppercase | `department_code.toUpperCase() == "RADIOLOGY"` |
| `toLowerCase()` | Convert to lowercase | `payer_type.toLowerCase() == "medicare"` |
| `charAt(n)` | Get character at index | `procedure_code.charAt(0) == "9"` |
| `substr(start, len)` | Extract substring | `procedure_code.substr(0, 2) == "99"` |
| `concat(str)` | Concatenate strings | `procedure_code.concat("-MOD")` |
| `split(sep)` | Split into array | `diagnosis_list.split(",")` |
| `replace(old, new)` | Replace substring | `procedure_code.replace("-", "")` |
| `test(regex)` | Regex match | `procedure_code.test(/^99[0-9]{3}$/)` |

### Array Methods

| Method | Description | Example |
|--------|-------------|---------|
| `join(sep)` | Join array to string | `diagnosis_codes.join(", ")` |
| `indexOf(val)` | Find index of value | `diagnosis_codes.indexOf("Z00.00") >= 0` |

---

## Built-in Functions

| Function | Description | Example |
|----------|-------------|---------|
| `parseInt(str)` | Parse string to integer | `parseInt(charge_amount) > 50000` |
| `parseFloat(str)` | Parse string to float | `parseFloat(rate) >= 1.5` |

---

## Example Rules

### Revenue Rules

```yaml
rules:
  # Undercharging Detection
  - id: REV_001
    name: MRI Undercharge Detection
    type: revenue
    description: Flags MRI procedures charged below Medicare minimum threshold
    severity: medium
    condition: >
      procedure_code.startsWith("70") &&
      payer_type == "MEDICARE" &&
      charge_amount < 50000
    message: "MRI charge ${charge_amount} is below Medicare minimum threshold of $500.00"
    tags: [imaging, medicare, undercharge]

  # Overcharging Detection
  - id: REV_002
    name: Excessive Charge Amount
    type: revenue
    description: Flags charges exceeding maximum allowed amount
    severity: high
    condition: >
      charge_amount > 1000000
    message: "Charge amount ${charge_amount} exceeds $10,000 maximum threshold"
    tags: [overcharge, audit]

  # Zero Charge Detection
  - id: REV_003
    name: Zero Dollar Charge
    type: revenue
    description: Identifies charges with zero amount that may indicate missed billing
    severity: low
    condition: >
      charge_amount === 0 &&
      payer_type not in ["CHARITY", "WRITE_OFF"]
    message: "Zero dollar charge detected for billable payer type"
    tags: [revenue-leakage, zero-charge]
```

### Compliance Rules

```yaml
rules:
  # Missing Diagnosis Code
  - id: COMP_001
    name: Missing Diagnosis Code
    type: compliance
    description: All charges must have at least one diagnosis code
    severity: high
    condition: >
      diagnosis_codes.indexOf("") === 0 ||
      diagnosis_codes.join("") === ""
    message: "Charge is missing required diagnosis code"
    tags: [compliance, diagnosis]

  # E&M Code Compliance
  - id: COMP_002
    name: High-Level E&M Code Review
    type: compliance
    description: High-level E&M codes require documented justification
    severity: medium
    condition: >
      procedure_code in ["99215", "99205", "99223", "99233"] &&
      payer_type in ["MEDICARE", "MEDICAID"]
    message: "High-level E&M code requires documentation review"
    tags: [e-and-m, documentation, audit-risk]

  # Modifier Validation
  - id: COMP_003
    name: Missing Required Modifier
    type: compliance
    description: Certain procedure codes require modifiers for Medicare
    severity: high
    condition: >
      procedure_code.test(/^[0-9]{5}$/) &&
      procedure_code in ["59400", "59510", "59610"] &&
      payer_type == "MEDICARE"
    message: "Global OB code may require modifier for Medicare billing"
    tags: [modifier, obstetrics, medicare]
```

### Audit Rules

```yaml
rules:
  # Duplicate Charge Pattern
  - id: AUD_001
    name: High-Volume Same-Day Charges
    type: audit
    description: Flags unusual volume of identical charges on same day
    severity: medium
    condition: >
      same_day_count > 5 &&
      procedure_code.startsWith("99")
    message: "Unusual volume of ${same_day_count} identical E&M charges on same day"
    tags: [duplicate, audit, pattern]

  # Weekend High-Dollar Charges
  - id: AUD_002
    name: Weekend High-Dollar Procedure
    type: audit
    description: High-dollar procedures on weekends require review
    severity: low
    condition: >
      is_weekend === true &&
      charge_amount > 500000 &&
      department_code not in ["ER", "ICU", "LABOR"]
    message: "High-dollar weekend charge outside emergency departments"
    tags: [weekend, audit, scheduling]

  # Diagnosis-Procedure Mismatch
  - id: AUD_003
    name: Cardiac Procedure Without Cardiac Diagnosis
    type: audit
    description: Cardiac procedures should have supporting diagnosis
    severity: high
    condition: >
      procedure_code.startsWith("33") &&
      diagnosis_codes.join(",").indexOf("I") < 0
    message: "Cardiac procedure code without cardiovascular diagnosis (I-codes)"
    tags: [diagnosis-mismatch, cardiology]
```

---

## Regex Patterns

Use JavaScript-style regex with the `test()` method:

```yaml
# Match CPT code format (5 digits)
condition: procedure_code.test(/^[0-9]{5}$/)

# Match E&M codes (992xx)
condition: procedure_code.test(/^992[0-9]{2}$/)

# Match ICD-10 format (letter + digits)
condition: diagnosis_codes.join(",").test(/[A-Z][0-9]{2}/)

# Case-insensitive match
condition: department_code.test(/radiology/i)
```

---

## Rule File Organization

```
config/
└── rules/
    ├── revenue.yml      # Revenue leakage rules
    ├── compliance.yml   # Regulatory compliance rules
    ├── audit.yml        # Audit flag rules
    └── custom.yml       # Organization-specific rules
```

---

## Rule Evaluation Flow

```
Charge Data
    │
    ▼
Load Rules (YAML)
    │
    ▼
Build Variable Context
    │
    ▼
For Each Rule:
    │
    ├─► Parse Condition (php-rule-parser)
    │
    ├─► Evaluate Expression
    │
    └─► If True → Create Insight
    │
    ▼
Return Insights
```

---

## Severity Levels

| Level | Description | Action |
|-------|-------------|--------|
| `critical` | Immediate compliance risk | Block/escalate |
| `high` | Likely revenue impact or compliance issue | Review required |
| `medium` | Potential issue requiring investigation | Flag for audit |
| `low` | Informational, pattern detection | Log for analysis |

---

## Best Practices

1. **Use descriptive IDs** - Prefix with type: `REV_`, `COMP_`, `AUD_`
2. **Keep conditions readable** - Use multi-line YAML (`>`) for complex expressions
3. **Test regex patterns** - Validate patterns before deployment
4. **Include context in messages** - Use `${variable}` placeholders
5. **Tag appropriately** - Enable filtering and reporting by category
6. **Start with high-severity rules** - Focus on critical compliance first

---

## Error Handling

Invalid rules are logged and skipped. Use the Rule validator:

```php
$rule = new Rule($condition, $variables);
if (!$rule->isValid()) {
    $error = $rule->getError();
    // Log error, skip rule
}
```

---

**Previous:** [Database ERD](database-erd.md)
**Next:** [API Endpoints](api-endpoints.md)
