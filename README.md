# SignalRite

Mini Revenue Integrity & Compliance Analyzer

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

## Documentation

See [docs/index.md](docs/index.md) for detailed design documentation.

## License

Released under the MIT license.
