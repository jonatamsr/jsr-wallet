# JSR Wallet API

A simple wallet API built with PHP/Hyperf/Swoole for managing accounts and balances in memory.

## Requirements

- Docker & Docker Compose

## Getting Started

```bash
# Build and start the application
docker compose up --build

# The API will be available at http://localhost:9501
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /reset | Reset all state |
| GET | /balance?account_id={id} | Get account balance |
| POST | /event | Create event (deposit, withdraw, transfer) |

## Running Tests

```bash
docker compose exec app composer test
```
