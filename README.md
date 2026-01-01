<div align="center">
  <a href="https://phpcompatible.dev">
    <img src="web/images/signalrite_centered_logo.png" alt="SignalRite" width="auto" height="128">
  </a>
  <p>
    <strong>
Mini Revenue Integrity & Compliance Analyzer</strong>
  </p>
</div>


## Purpose

SignalRite is a **small, focused backend service** that simulates how healthcare financial systems identify **revenue leakage, compliance risk, and billing anomalies** using **rules-driven analysis** and **explainable outputs**.

The goal is **not** to recreate a full billing system, but to demonstrate:
- How healthcare charge data is evaluated
- How rules and compliance logic are applied
- How actionable financial insights are produced

## Tech Stack

- **Symfony 3**
- **PHP 7.2**
- **Doctrine ORM**
- **Oracle Database**

## Getting Started

### Prerequisites

- Docker and Docker Compose

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-org/signal-rite.git
   cd signal-rite
   ```

2. Start the services:
   ```bash
   docker-compose up -d
   ```

3. Install dependencies:
   ```bash
   docker-compose exec php-cli composer install
   ```

4. Access the application at `http://localhost:8000`

### Services

| Service  | Port | Description              |
|----------|------|--------------------------|
| Web      | 8000 | Symfony application      |
| Oracle   | 1521 | Oracle XE database       |
| MailHog  | 8025 | Email testing UI         |

### Testing Database Connection

#### 1. Check Oracle Container Health

First, ensure the Oracle container is healthy:

```bash
docker-compose ps
```

The `oracle-xe` container should show as `healthy`. Oracle can take 1-2 minutes to initialize on first start.

#### 2. Test Doctrine Connection

Run the Doctrine schema validation command:

```bash
docker-compose exec php-cli bin/console doctrine:schema:validate
```

If the connection is successful, you'll see output about the mapping status.

#### 3. Test with a Simple Query

Run a raw SQL query through Doctrine:

```bash
docker-compose exec php-cli bin/console doctrine:query:sql "SELECT 1 FROM DUAL"
```

A successful connection returns:

```
array(1) {
  [0]=>
  array(1) {
    [1]=>
    string(1) "1"
  }
}
```

#### 4. View Database Schema

List existing tables:

```bash
docker-compose exec php-cli bin/console doctrine:schema:update --dump-sql
```

#### Troubleshooting

| Issue | Solution |
|-------|----------|
| Connection refused | Wait for Oracle to fully start (`docker logs oracle-xe`) |
| ORA-12541: No listener | Oracle is still initializing, wait 1-2 minutes |
| Authentication failed | Check `DATABASE_USER` and `DATABASE_PASSWORD` in `docker-compose.yml` |

## Documentation

See [docs/index.md](docs/index.md) for detailed design documentation.

## License

Released under the MIT license.
