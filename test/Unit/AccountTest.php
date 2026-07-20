<?php

declare(strict_types=1);

namespace HyperfTest\Unit;

use App\Exception\InsufficientFundsException;
use App\Model\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testCreateAccountStartsWithZeroBalance(): void
    {
        $account = new Account('100');

        $this->assertSame('100', $account->getId());
        $this->assertSame(0, $account->getBalance());
    }

    public function testDeposit(): void
    {
        $account = new Account('100');

        $account->deposit(10);

        $this->assertSame(10, $account->getBalance());
    }

    public function testMultipleDeposits(): void
    {
        $account = new Account('100');

        $account->deposit(10);
        $account->deposit(20);

        $this->assertSame(30, $account->getBalance());
    }

    public function testWithdraw(): void
    {
        $account = new Account('100');
        $account->deposit(20);

        $account->withdraw(5);

        $this->assertSame(15, $account->getBalance());
    }

    public function testWithdrawEntireBalance(): void
    {
        $account = new Account('100');
        $account->deposit(10);

        $account->withdraw(10);

        $this->assertSame(0, $account->getBalance());
    }

    public function testWithdrawInsufficientFundsThrowsException(): void
    {
        $account = new Account('100');
        $account->deposit(5);

        $this->expectException(InsufficientFundsException::class);

        $account->withdraw(10);
    }

    public function testToArray(): void
    {
        $account = new Account('100');
        $account->deposit(25);

        $this->assertSame(['id' => '100', 'balance' => 25], $account->toArray());
    }

    public function testDepositZeroThrowsException(): void
    {
        $account = new Account('100');

        $this->expectException(\App\Exception\InvalidAmountException::class);

        $account->deposit(0);
    }

    public function testDepositNegativeThrowsException(): void
    {
        $account = new Account('100');

        $this->expectException(\App\Exception\InvalidAmountException::class);

        $account->deposit(-5);
    }

    public function testWithdrawZeroThrowsException(): void
    {
        $account = new Account('100');
        $account->deposit(10);

        $this->expectException(\App\Exception\InvalidAmountException::class);

        $account->withdraw(0);
    }

    public function testWithdrawNegativeThrowsException(): void
    {
        $account = new Account('100');
        $account->deposit(10);

        $this->expectException(\App\Exception\InvalidAmountException::class);

        $account->withdraw(-5);
    }
}
