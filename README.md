# JSR Wallet API

Simple wallet REST API for the EBANX take-home assignment. Manages accounts and balances in-memory using Swoole's persistent process state.

## Stack

- PHP 8.4
- Hyperf 3.2
- Swoole (single worker, in-memory state)
- Docker

## Running Locally

```bash
docker compose up --build
# API available at http://localhost:9501
```

## Running Tests

```bash
docker compose exec app composer test
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /reset | Reset all accounts |
| GET | /balance?account_id={id} | Get account balance (200 or 404) |
| POST | /event | Deposit, withdraw, or transfer |

### Event Payloads

**Deposit:**
```json
{"type": "deposit", "destination": "100", "amount": 10}
```

**Withdraw:**
```json
{"type": "withdraw", "origin": "100", "amount": 5}
```

**Transfer:**
```json
{"type": "transfer", "origin": "100", "destination": "300", "amount": 15}
```

## Architecture

```
app/
├── Controller/    → HTTP layer (request/response only)
├── Service/       → Business orchestration
├── Repository/    → In-memory storage (static array)
├── Model/         → Account entity + TransferResult VO
└── Exception/     → Domain exceptions
```

**Key decisions:**
- **Single Swoole worker** (`WORKER_NUM=1`): guarantees consistent in-memory state across requests without shared-memory complexity.
- **No database**: state lives in a static PHP array, persisted by the long-running Swoole process. `POST /reset` clears it.
- **Layered separation**: Controllers handle HTTP only, Services orchestrate, Entities hold business rules.

## Deploy

Deployed on Railway: `https://jsr-wallet-production.up.railway.app`
