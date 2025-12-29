# Oracle SQL for MySQL Developers

A practical guide to Oracle SQL dialect for developers coming from MySQL.

---

## Quick Reference: Command Equivalents

| Task | MySQL | Oracle |
|------|-------|--------|
| List all tables | `SHOW TABLES` | `SELECT table_name FROM user_tables` |
| Describe table structure | `DESCRIBE table_name` | `DESC table_name` or `DESCRIBE table_name` |
| Show databases | `SHOW DATABASES` | `SELECT * FROM all_users` (users ≈ schemas) |
| Use database | `USE database_name` | `ALTER SESSION SET CURRENT_SCHEMA = schema_name` |
| Show current database | `SELECT DATABASE()` | `SELECT SYS_CONTEXT('USERENV','CURRENT_SCHEMA') FROM dual` |
| Show create table | `SHOW CREATE TABLE t` | Query `DBMS_METADATA.GET_DDL('TABLE','T')` |
| Show indexes | `SHOW INDEX FROM table` | `SELECT * FROM user_indexes WHERE table_name = 'TABLE'` |
| Show columns | `SHOW COLUMNS FROM t` | `SELECT * FROM user_tab_columns WHERE table_name = 'T'` |

---

## 1. The DUAL Table

Oracle requires a `FROM` clause for all `SELECT` statements. When you just need to evaluate an expression or call a function, use the `DUAL` dummy table:

```sql
-- MySQL
SELECT 1 + 1;
SELECT NOW();

-- Oracle
SELECT 1 + 1 FROM dual;
SELECT SYSDATE FROM dual;
```

---

## 2. Limiting Results: LIMIT vs FETCH/ROWNUM

### MySQL Style
```sql
SELECT * FROM employees LIMIT 10;
SELECT * FROM employees LIMIT 10 OFFSET 20;
```

### Oracle 12c+ (Recommended)
```sql
-- First 10 rows
SELECT * FROM employees FETCH FIRST 10 ROWS ONLY;

-- With offset (skip 20, get 10)
SELECT * FROM employees OFFSET 20 ROWS FETCH NEXT 10 ROWS ONLY;

-- Top N with ties
SELECT * FROM employees ORDER BY salary DESC
FETCH FIRST 5 ROWS WITH TIES;
```

### Oracle Legacy (Pre-12c)
```sql
-- Using ROWNUM (be careful with ORDER BY!)
SELECT * FROM (
    SELECT * FROM employees ORDER BY hire_date
) WHERE ROWNUM <= 10;

-- With offset using ROW_NUMBER()
SELECT * FROM (
    SELECT e.*, ROW_NUMBER() OVER (ORDER BY hire_date) rn
    FROM employees e
) WHERE rn BETWEEN 21 AND 30;
```

---

## 3. String Handling

### Concatenation
```sql
-- MySQL
SELECT CONCAT(first_name, ' ', last_name) FROM employees;
SELECT CONCAT_WS(' ', first_name, middle_name, last_name) FROM employees;

-- Oracle (use || operator)
SELECT first_name || ' ' || last_name FROM employees;

-- Oracle also has CONCAT but only takes 2 arguments
SELECT CONCAT(CONCAT(first_name, ' '), last_name) FROM employees;
```

### String Functions Comparison

| MySQL | Oracle | Description |
|-------|--------|-------------|
| `LENGTH()` | `LENGTH()` | Character length |
| `CHAR_LENGTH()` | `LENGTH()` | Character length |
| `SUBSTRING(s, pos, len)` | `SUBSTR(s, pos, len)` | Extract substring |
| `LOCATE(substr, str)` | `INSTR(str, substr)` | Find position (note: args reversed!) |
| `LPAD()` / `RPAD()` | `LPAD()` / `RPAD()` | Pad strings (same) |
| `TRIM()` | `TRIM()` | Remove whitespace (same) |
| `UPPER()` / `LOWER()` | `UPPER()` / `LOWER()` | Case conversion (same) |
| `REPLACE()` | `REPLACE()` | Replace text (same) |
| `IFNULL(a, b)` | `NVL(a, b)` | Null substitution |
| `COALESCE()` | `COALESCE()` | First non-null (same) |

### Examples
```sql
-- MySQL
SELECT SUBSTRING(name, 1, 3) FROM products;
SELECT IFNULL(nickname, 'N/A') FROM users;

-- Oracle
SELECT SUBSTR(name, 1, 3) FROM products;
SELECT NVL(nickname, 'N/A') FROM users;
```

---

## 4. Date and Time

### Getting Current Date/Time
```sql
-- MySQL
SELECT NOW();              -- Date and time
SELECT CURDATE();          -- Date only
SELECT CURTIME();          -- Time only
SELECT UNIX_TIMESTAMP();   -- Epoch seconds

-- Oracle
SELECT SYSDATE FROM dual;           -- Date and time (to seconds)
SELECT SYSTIMESTAMP FROM dual;      -- Date with fractional seconds + timezone
SELECT CURRENT_DATE FROM dual;      -- Session timezone date
SELECT CURRENT_TIMESTAMP FROM dual; -- Session timezone timestamp
```

### Date Arithmetic
```sql
-- MySQL
SELECT DATE_ADD(hire_date, INTERVAL 30 DAY) FROM employees;
SELECT DATE_SUB(NOW(), INTERVAL 1 MONTH);
SELECT DATEDIFF(end_date, start_date) FROM projects;

-- Oracle (add/subtract days directly as numbers)
SELECT hire_date + 30 FROM employees;
SELECT SYSDATE - 30 FROM dual;

-- Oracle: months and years
SELECT ADD_MONTHS(hire_date, 1) FROM employees;
SELECT ADD_MONTHS(SYSDATE, 12) FROM dual;

-- Difference in days (just subtract)
SELECT end_date - start_date FROM projects;

-- Difference in months
SELECT MONTHS_BETWEEN(end_date, start_date) FROM projects;
```

### Date Formatting
```sql
-- MySQL
SELECT DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') FROM orders;
SELECT DATE_FORMAT(created_at, '%d/%m/%Y') FROM orders;

-- Oracle (uses TO_CHAR with different format codes)
SELECT TO_CHAR(created_at, 'YYYY-MM-DD HH24:MI:SS') FROM orders;
SELECT TO_CHAR(created_at, 'DD/MM/YYYY') FROM orders;
```

### Common Oracle Date Format Codes
| Code | Meaning | Example |
|------|---------|---------|
| `YYYY` | 4-digit year | 2024 |
| `MM` | Month (01-12) | 07 |
| `DD` | Day (01-31) | 15 |
| `HH24` | Hour (00-23) | 14 |
| `HH` | Hour (01-12) | 02 |
| `MI` | Minutes | 30 |
| `SS` | Seconds | 45 |
| `DAY` | Full day name | MONDAY |
| `MON` | Abbreviated month | JUL |
| `MONTH` | Full month name | JULY |

### Parsing Strings to Dates
```sql
-- MySQL
SELECT STR_TO_DATE('2024-07-15', '%Y-%m-%d');

-- Oracle
SELECT TO_DATE('2024-07-15', 'YYYY-MM-DD') FROM dual;
SELECT TO_TIMESTAMP('2024-07-15 14:30:00', 'YYYY-MM-DD HH24:MI:SS') FROM dual;
```

---

## 5. Auto-Increment vs Sequences

### MySQL Auto-Increment
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

INSERT INTO users (name) VALUES ('John');
SELECT LAST_INSERT_ID();
```

### Oracle 12c+ Identity Columns (Recommended)
```sql
CREATE TABLE users (
    id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR2(100)
);

-- Or with more control
CREATE TABLE users (
    id NUMBER GENERATED BY DEFAULT ON NULL AS IDENTITY 
        (START WITH 1 INCREMENT BY 1) PRIMARY KEY,
    name VARCHAR2(100)
);

INSERT INTO users (name) VALUES ('John');
```

### Oracle Sequences (Traditional Approach)
```sql
-- Create sequence
CREATE SEQUENCE users_seq START WITH 1 INCREMENT BY 1;

-- Create table
CREATE TABLE users (
    id NUMBER PRIMARY KEY,
    name VARCHAR2(100)
);

-- Insert using sequence
INSERT INTO users (id, name) VALUES (users_seq.NEXTVAL, 'John');

-- Get current value
SELECT users_seq.CURRVAL FROM dual;

-- Get next value without using it
SELECT users_seq.NEXTVAL FROM dual;
```

---

## 6. Data Types Mapping

| MySQL | Oracle | Notes |
|-------|--------|-------|
| `INT` / `INTEGER` | `NUMBER(10)` | Or just `NUMBER` |
| `BIGINT` | `NUMBER(19)` | |
| `SMALLINT` | `NUMBER(5)` | |
| `TINYINT` | `NUMBER(3)` | |
| `DECIMAL(p,s)` | `NUMBER(p,s)` | |
| `FLOAT` / `DOUBLE` | `BINARY_FLOAT` / `BINARY_DOUBLE` | Or `NUMBER` |
| `VARCHAR(n)` | `VARCHAR2(n)` | Max 4000 bytes (or 32767 with extended) |
| `CHAR(n)` | `CHAR(n)` | Same |
| `TEXT` | `CLOB` | Character Large Object |
| `BLOB` | `BLOB` | Same |
| `DATETIME` | `DATE` | Oracle DATE includes time! |
| `TIMESTAMP` | `TIMESTAMP` | Same |
| `DATE` | `DATE` | Oracle DATE has time component |
| `TIME` | `INTERVAL DAY TO SECOND` | No direct equivalent |
| `BOOLEAN` | `NUMBER(1)` or `CHAR(1)` | No native boolean in SQL |
| `ENUM(...)` | Check constraint | No native enum |
| `JSON` | `JSON` (21c+) or `CLOB` | |

### Important: Oracle DATE vs MySQL DATE
```sql
-- MySQL DATE is date-only, DATETIME includes time

-- Oracle DATE always includes time (just defaults to midnight)
SELECT TO_CHAR(SYSDATE, 'YYYY-MM-DD HH24:MI:SS') FROM dual;
-- Returns: 2024-07-15 14:30:45

-- Truncate to date-only if needed
SELECT TRUNC(SYSDATE) FROM dual;
```

---

## 7. Conditional Expressions

### IF/CASE Equivalents
```sql
-- MySQL IF()
SELECT IF(status = 'A', 'Active', 'Inactive') FROM users;

-- Oracle: Use CASE or DECODE
SELECT CASE WHEN status = 'A' THEN 'Active' ELSE 'Inactive' END FROM users;

-- Oracle DECODE (simple equality only)
SELECT DECODE(status, 'A', 'Active', 'Inactive') FROM users;

-- Multiple conditions with DECODE
SELECT DECODE(status, 
    'A', 'Active',
    'I', 'Inactive', 
    'P', 'Pending',
    'Unknown'  -- default
) FROM users;
```

### IFNULL / NVL / COALESCE
```sql
-- MySQL
SELECT IFNULL(phone, 'No phone') FROM contacts;

-- Oracle
SELECT NVL(phone, 'No phone') FROM contacts;

-- Both support COALESCE (returns first non-null)
SELECT COALESCE(mobile, home_phone, work_phone, 'No phone') FROM contacts;
```

### NVL2 (Oracle-specific)
```sql
-- Returns one value if not null, another if null
SELECT NVL2(commission_pct, 'Has Commission', 'No Commission') FROM employees;

-- Equivalent in MySQL
SELECT IF(commission_pct IS NOT NULL, 'Has Commission', 'No Commission') FROM employees;
```

### NULLIF
```sql
-- Same in both - returns NULL if arguments are equal
SELECT NULLIF(value, 0) FROM data;  -- Returns NULL if value is 0
```

---

## 8. Joins and Subqueries

### Join Syntax
Both MySQL and Oracle support standard ANSI join syntax (recommended):

```sql
-- Works in both
SELECT e.name, d.department_name
FROM employees e
INNER JOIN departments d ON e.dept_id = d.id;

SELECT e.name, d.department_name
FROM employees e
LEFT JOIN departments d ON e.dept_id = d.id;
```

### Oracle Legacy Join Syntax (Avoid in New Code)
```sql
-- Old Oracle style (you may see this in legacy code)
SELECT e.name, d.department_name
FROM employees e, departments d
WHERE e.dept_id = d.id;

-- Old Oracle outer join (+ operator)
SELECT e.name, d.department_name
FROM employees e, departments d
WHERE e.dept_id = d.id(+);  -- LEFT JOIN equivalent
```

### Subquery Differences
```sql
-- MySQL allows LIMIT in subqueries
SELECT * FROM orders WHERE customer_id IN (
    SELECT id FROM customers ORDER BY created_at DESC LIMIT 10
);

-- Oracle: Use FETCH FIRST or ROWNUM
SELECT * FROM orders WHERE customer_id IN (
    SELECT id FROM (
        SELECT id FROM customers ORDER BY created_at DESC
    ) WHERE ROWNUM <= 10
);

-- Or with FETCH (12c+)
SELECT * FROM orders WHERE customer_id IN (
    SELECT id FROM customers ORDER BY created_at DESC
    FETCH FIRST 10 ROWS ONLY
);
```

---

## 9. INSERT, UPDATE, DELETE Differences

### INSERT Multiple Rows
```sql
-- MySQL: Single INSERT with multiple value sets
INSERT INTO users (name, email) VALUES 
    ('John', 'john@example.com'),
    ('Jane', 'jane@example.com'),
    ('Bob', 'bob@example.com');

-- Oracle: INSERT ALL
INSERT ALL
    INTO users (name, email) VALUES ('John', 'john@example.com')
    INTO users (name, email) VALUES ('Jane', 'jane@example.com')
    INTO users (name, email) VALUES ('Bob', 'bob@example.com')
SELECT 1 FROM dual;

-- Or use multiple INSERT statements
INSERT INTO users (name, email) VALUES ('John', 'john@example.com');
INSERT INTO users (name, email) VALUES ('Jane', 'jane@example.com');
```

### INSERT ... ON DUPLICATE KEY vs MERGE
```sql
-- MySQL
INSERT INTO products (id, name, quantity) 
VALUES (1, 'Widget', 100)
ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity);

-- Oracle: MERGE statement
MERGE INTO products p
USING (SELECT 1 AS id, 'Widget' AS name, 100 AS quantity FROM dual) src
ON (p.id = src.id)
WHEN MATCHED THEN
    UPDATE SET p.quantity = p.quantity + src.quantity
WHEN NOT MATCHED THEN
    INSERT (id, name, quantity) VALUES (src.id, src.name, src.quantity);
```

### UPDATE with JOIN
```sql
-- MySQL
UPDATE orders o
JOIN customers c ON o.customer_id = c.id
SET o.status = 'VIP'
WHERE c.tier = 'gold';

-- Oracle
UPDATE orders o
SET status = 'VIP'
WHERE customer_id IN (
    SELECT id FROM customers WHERE tier = 'gold'
);

-- Or using MERGE
MERGE INTO orders o
USING (SELECT id FROM customers WHERE tier = 'gold') c
ON (o.customer_id = c.id)
WHEN MATCHED THEN UPDATE SET o.status = 'VIP';
```

### DELETE with JOIN
```sql
-- MySQL
DELETE o FROM orders o
JOIN customers c ON o.customer_id = c.id
WHERE c.status = 'inactive';

-- Oracle
DELETE FROM orders
WHERE customer_id IN (
    SELECT id FROM customers WHERE status = 'inactive'
);
```

---

## 10. Transaction Control

```sql
-- MySQL
START TRANSACTION;
-- or
BEGIN;

-- Oracle
-- Transactions start implicitly with first DML
-- No START TRANSACTION or BEGIN needed

-- Both use:
COMMIT;
ROLLBACK;
SAVEPOINT savepoint_name;
ROLLBACK TO savepoint_name;
```

### Autocommit
```sql
-- MySQL: Check/set autocommit
SELECT @@autocommit;
SET autocommit = 0;

-- Oracle: Check autocommit (in SQL*Plus)
SHOW AUTOCOMMIT;
SET AUTOCOMMIT ON;
SET AUTOCOMMIT OFF;

-- In application code, autocommit is typically controlled by the driver/connection
```

---

## 11. Table Creation and Modification

### Creating Tables
```sql
-- MySQL
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE,
    salary DECIMAL(10,2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

-- Oracle
CREATE TABLE employees (
    id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name VARCHAR2(100) NOT NULL,
    email VARCHAR2(255) UNIQUE,
    salary NUMBER(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Create index separately
CREATE INDEX idx_name ON employees(name);
```

### Modifying Tables
```sql
-- MySQL
ALTER TABLE employees ADD COLUMN department_id INT;
ALTER TABLE employees MODIFY COLUMN name VARCHAR(200);
ALTER TABLE employees DROP COLUMN temp_field;
ALTER TABLE employees RENAME COLUMN old_name TO new_name;

-- Oracle
ALTER TABLE employees ADD department_id NUMBER;
ALTER TABLE employees MODIFY name VARCHAR2(200);
ALTER TABLE employees DROP COLUMN temp_field;
ALTER TABLE employees RENAME COLUMN old_name TO new_name;
```

### Viewing Table Structure
```sql
-- MySQL
DESCRIBE employees;
SHOW CREATE TABLE employees;
SHOW COLUMNS FROM employees;

-- Oracle
DESCRIBE employees;
-- or
DESC employees;

-- Get DDL
SELECT DBMS_METADATA.GET_DDL('TABLE', 'EMPLOYEES') FROM dual;

-- Query column info
SELECT column_name, data_type, data_length, nullable
FROM user_tab_columns
WHERE table_name = 'EMPLOYEES';
```

---

## 12. Schema and Database Concepts

**Key Difference:** In MySQL, databases and schemas are synonymous. In Oracle, a schema is tied to a user—each user has their own schema.

```sql
-- MySQL
CREATE DATABASE myapp;
USE myapp;
DROP DATABASE myapp;

-- Oracle
-- Create a user (which creates a schema)
CREATE USER myapp IDENTIFIED BY password;
GRANT CONNECT, RESOURCE TO myapp;

-- Switch to schema context
ALTER SESSION SET CURRENT_SCHEMA = myapp;

-- Work in another user's schema
SELECT * FROM other_user.their_table;

-- Drop user and their schema
DROP USER myapp CASCADE;
```

### Listing Objects
```sql
-- MySQL
SHOW TABLES;
SHOW DATABASES;

-- Oracle: Objects in your schema
SELECT table_name FROM user_tables;
SELECT view_name FROM user_views;
SELECT sequence_name FROM user_sequences;
SELECT index_name FROM user_indexes;

-- Objects you have access to
SELECT owner, table_name FROM all_tables WHERE owner = 'SOME_USER';

-- All objects in database (DBA only)
SELECT owner, table_name FROM dba_tables;
```

---

## 13. Aggregate Functions and GROUP BY

Most aggregate functions work identically, but there are some Oracle-specific features:

### String Aggregation
```sql
-- MySQL
SELECT department_id, GROUP_CONCAT(name SEPARATOR ', ') 
FROM employees 
GROUP BY department_id;

-- Oracle (11g R2+)
SELECT department_id, LISTAGG(name, ', ') WITHIN GROUP (ORDER BY name)
FROM employees
GROUP BY department_id;

-- Handle potential overflow (12c R2+)
SELECT department_id, 
    LISTAGG(name, ', ' ON OVERFLOW TRUNCATE '...' WITH COUNT) 
    WITHIN GROUP (ORDER BY name)
FROM employees
GROUP BY department_id;
```

### DISTINCT in Aggregates
```sql
-- Works in both
SELECT COUNT(DISTINCT department_id) FROM employees;

-- Oracle LISTAGG with DISTINCT (19c+)
SELECT LISTAGG(DISTINCT department, ', ') WITHIN GROUP (ORDER BY department)
FROM employees;
```

---

## 14. Common Table Expressions (CTEs)

Both MySQL 8.0+ and Oracle support CTEs with similar syntax:

```sql
-- Works in both
WITH dept_totals AS (
    SELECT department_id, SUM(salary) AS total_salary
    FROM employees
    GROUP BY department_id
)
SELECT d.department_name, dt.total_salary
FROM departments d
JOIN dept_totals dt ON d.id = dt.department_id;

-- Recursive CTE (both support)
WITH RECURSIVE org_chart AS (
    SELECT id, name, manager_id, 1 AS level
    FROM employees WHERE manager_id IS NULL
    UNION ALL
    SELECT e.id, e.name, e.manager_id, oc.level + 1
    FROM employees e
    JOIN org_chart oc ON e.manager_id = oc.id
)
SELECT * FROM org_chart;
```

**Note:** Oracle uses `WITH` for recursive CTEs (no `RECURSIVE` keyword needed):
```sql
-- Oracle recursive CTE
WITH org_chart (id, name, manager_id, level) AS (
    SELECT id, name, manager_id, 1 FROM employees WHERE manager_id IS NULL
    UNION ALL
    SELECT e.id, e.name, e.manager_id, oc.level + 1
    FROM employees e
    JOIN org_chart oc ON e.manager_id = oc.id
)
SELECT * FROM org_chart;
```

---

## 15. Window Functions

Both databases support window functions with similar syntax:

```sql
-- Works in both
SELECT 
    name,
    department_id,
    salary,
    ROW_NUMBER() OVER (PARTITION BY department_id ORDER BY salary DESC) AS rank,
    SUM(salary) OVER (PARTITION BY department_id) AS dept_total,
    AVG(salary) OVER () AS company_avg
FROM employees;
```

### Oracle-Specific Window Functions
```sql
-- RATIO_TO_REPORT
SELECT name, salary,
    RATIO_TO_REPORT(salary) OVER (PARTITION BY department_id) AS salary_pct
FROM employees;

-- FIRST_VALUE / LAST_VALUE
SELECT name, salary,
    FIRST_VALUE(name) OVER (PARTITION BY dept_id ORDER BY salary DESC) AS top_earner
FROM employees;
```

---

## 16. Error Handling in PL/SQL vs MySQL Procedures

### MySQL Stored Procedure
```sql
DELIMITER //
CREATE PROCEDURE transfer_funds(
    IN from_account INT,
    IN to_account INT,
    IN amount DECIMAL(10,2)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Transfer failed';
    END;
    
    START TRANSACTION;
    UPDATE accounts SET balance = balance - amount WHERE id = from_account;
    UPDATE accounts SET balance = balance + amount WHERE id = to_account;
    COMMIT;
END //
DELIMITER ;
```

### Oracle PL/SQL Procedure
```sql
CREATE OR REPLACE PROCEDURE transfer_funds(
    p_from_account IN NUMBER,
    p_to_account IN NUMBER,
    p_amount IN NUMBER
) AS
BEGIN
    UPDATE accounts SET balance = balance - p_amount WHERE id = p_from_account;
    UPDATE accounts SET balance = balance + p_amount WHERE id = p_to_account;
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20001, 'Transfer failed: ' || SQLERRM);
END;
/
```

---

## 17. Useful Oracle Dictionary Views

Replace MySQL `SHOW` commands with these queries:

```sql
-- All your tables
SELECT table_name FROM user_tables;

-- Tables with row counts (approximate)
SELECT table_name, num_rows FROM user_tables;

-- All your indexes
SELECT index_name, table_name, uniqueness FROM user_indexes;

-- Index columns
SELECT index_name, column_name, column_position
FROM user_ind_columns
WHERE table_name = 'EMPLOYEES'
ORDER BY index_name, column_position;

-- Constraints
SELECT constraint_name, constraint_type, table_name
FROM user_constraints
WHERE table_name = 'EMPLOYEES';

-- Foreign keys with referenced table
SELECT a.constraint_name, a.table_name, b.table_name AS referenced_table
FROM user_constraints a
JOIN user_constraints b ON a.r_constraint_name = b.constraint_name
WHERE a.constraint_type = 'R';

-- Sequences
SELECT sequence_name, last_number, increment_by FROM user_sequences;

-- Source code (procedures, functions, packages)
SELECT * FROM user_source WHERE name = 'MY_PROCEDURE' ORDER BY line;

-- Currently running queries
SELECT sql_text FROM v$sql WHERE users_executing > 0;
```

---

## 18. Quick Tips and Gotchas

1. **Empty strings vs NULL:** In Oracle, empty string (`''`) is treated as NULL. MySQL treats them as different.
   ```sql
   -- Oracle
   SELECT * FROM users WHERE name IS NULL;  -- Also finds empty strings!
   ```

2. **Case sensitivity:** Oracle stores identifiers in UPPERCASE by default. Use double quotes for mixed case (avoid if possible).
   ```sql
   CREATE TABLE "MyTable" (...);  -- Creates case-sensitive name
   SELECT * FROM "MyTable";        -- Must use exact case
   ```

3. **String comparison:** Oracle may pad CHAR columns for comparison; use VARCHAR2 to avoid issues.

4. **No UNSIGNED:** Oracle doesn't have unsigned integers. Use CHECK constraints instead:
   ```sql
   CREATE TABLE t (quantity NUMBER CHECK (quantity >= 0));
   ```

5. **Comments in DDL:**
   ```sql
   COMMENT ON TABLE employees IS 'Employee master data';
   COMMENT ON COLUMN employees.salary IS 'Annual salary in USD';
   ```

6. **Getting the row just inserted:**
   ```sql
   -- Use RETURNING clause
   INSERT INTO employees (name, salary) 
   VALUES ('John', 50000)
   RETURNING id INTO v_new_id;  -- PL/SQL variable
   ```

---

## Appendix: Quick Conversion Cheat Sheet

| MySQL | Oracle | Notes |
|-------|--------|-------|
| `SHOW TABLES` | `SELECT * FROM user_tables` | |
| `DESCRIBE t` | `DESC t` | Same |
| `LIMIT n` | `FETCH FIRST n ROWS ONLY` | 12c+ |
| `NOW()` | `SYSDATE` | |
| `IFNULL()` | `NVL()` | |
| `IF(cond, a, b)` | `CASE WHEN cond THEN a ELSE b END` | |
| `AUTO_INCREMENT` | `GENERATED AS IDENTITY` | 12c+ |
| `LAST_INSERT_ID()` | `sequence.CURRVAL` | |
| `CONCAT()` | `\|\|` operator | |
| `SUBSTRING()` | `SUBSTR()` | |
| `GROUP_CONCAT()` | `LISTAGG()` | |
| `LOCATE(a, b)` | `INSTR(b, a)` | Args reversed! |
| `DATE_FORMAT()` | `TO_CHAR()` | Different format codes |
| `STR_TO_DATE()` | `TO_DATE()` | Different format codes |
| `DATEDIFF(a, b)` | `a - b` | Direct subtraction |
| `DATE_ADD(d, INTERVAL n DAY)` | `d + n` | Add number directly |
| `VARCHAR` | `VARCHAR2` | |
| `TEXT` | `CLOB` | |
| `BOOLEAN` | `NUMBER(1)` | No native boolean |

---

Happy querying! 🎯
