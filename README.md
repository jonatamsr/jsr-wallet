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

## Technical Decisions

### Why Hyperf + Swoole?

The spec says *"Durability IS NOT a requirement"*: no database needed. Swoole runs PHP as a long-lived process (like Node.js), so a simple static array persists state between requests naturally. No external storage, no serialization overhead, no infrastructure dependencies.

Hyperf was chosen over plain Swoole for its routing, DI container, and testing client, just enough framework to avoid reinventing the wheel without over-engineering.

### Single Worker (`WORKER_NUM=1`)

Swoole can spawn multiple workers, each with its own memory space. With in-memory state in a static array, multiple workers would mean inconsistent state between requests. A single worker guarantees atomicity for all operations without needing locks, shared memory, or any synchronization mechanism.

`MAX_REQUEST=0` ensures the worker never restarts (which would wipe state).

### In-Memory Storage (Static Array)

The simplest possible persistence that satisfies the spec. A `private static array $accounts` in the Repository class. No ORM, no Redis, no file system. `POST /reset` simply reassigns it to `[]`.

### Layered Architecture (without over-engineering)

```
Controller → Service → Entity + Repository
```

- **Controller**: HTTP translation only. Receives request, calls service, returns response with correct status code. Zero business logic.
- **Service**: Orchestration. Finds/creates accounts via repository, delegates mutations to the entity, persists.
- **Entity (Account)**: Owns all business rules `deposit()`, `withdraw()` with balance validation, `assertPositiveAmount()` guard.
- **Repository**: Pure storage abstraction over the static array.


### Transfers Are Atomic

Since we run a single worker with synchronous execution, a transfer (debit origin + credit destination) is inherently atomic — no partial state is possible. No need for database transactions or locks.

### Dynamic Port

`config/autoload/server.php` reads `env('PORT', 9501)`. Locally defaults to 9501; Railway injects its own port at runtime.

## Deploy

Deployed on Railway: `https://jsr-wallet-production.up.railway.app`
