<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\InsufficientFundsException;

class Account
{
    private int $balance = 0;

    public function __construct(
        private readonly string $id,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function deposit(int $amount): void
    {
        $this->assertPositiveAmount($amount);

        $this->balance += $amount;
    }

    public function withdraw(int $amount): void
    {
        $this->assertPositiveAmount($amount);

        if ($amount > $this->balance) {
            throw new InsufficientFundsException($this->id, $this->balance, $amount);
        }

        $this->balance -= $amount;
    }

    private function assertPositiveAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'balance' => $this->balance,
        ];
    }
}
