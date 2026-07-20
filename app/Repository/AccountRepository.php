<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Account;

class AccountRepository
{
    /** @var array<string, Account> */
    private static array $accounts = [];

    public function find(string $id): ?Account
    {
        return self::$accounts[$id] ?? null;
    }

    public function save(Account $account): void
    {
        self::$accounts[$account->getId()] = $account;
    }

    public function reset(): void
    {
        self::$accounts = [];
    }
}
