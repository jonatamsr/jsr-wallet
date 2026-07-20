<?php

declare(strict_types=1);

namespace HyperfTest\Unit;

use App\Exception\AccountNotFoundException;
use App\Model\TransferResult;
use App\Repository\AccountRepository;
use App\Service\AccountService;
use PHPUnit\Framework\TestCase;

class AccountServiceTest extends TestCase
{
    private AccountService $service;
    private AccountRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new AccountRepository();
        $this->repository->reset();
        $this->service = new AccountService($this->repository);
    }

    public function testDepositCreatesNewAccount(): void
    {
        $account = $this->service->deposit('100', 10);

        $this->assertSame('100', $account->getId());
        $this->assertSame(10, $account->getBalance());
    }

    public function testDepositToExistingAccountIncrementsBalance(): void
    {
        $this->service->deposit('100', 10);

        $account = $this->service->deposit('100', 20);

        $this->assertSame(30, $account->getBalance());
    }

    public function testWithdrawFromExistingAccount(): void
    {
        $this->service->deposit('100', 20);

        $account = $this->service->withdraw('100', 5);

        $this->assertSame(15, $account->getBalance());
    }

    public function testWithdrawFromNonExistingAccountThrowsException(): void
    {
        $this->expectException(AccountNotFoundException::class);

        $this->service->withdraw('999', 10);
    }

    public function testTransferBetweenExistingAccounts(): void
    {
        $this->service->deposit('100', 50);
        $this->service->deposit('300', 10);

        $result = $this->service->transfer('100', '300', 15);

        $this->assertInstanceOf(TransferResult::class, $result);
        $this->assertSame(35, $result->getOrigin()->getBalance());
        $this->assertSame(25, $result->getDestination()->getBalance());
    }

    public function testTransferCreatesDestinationIfNotExists(): void
    {
        $this->service->deposit('100', 50);

        $result = $this->service->transfer('100', '300', 15);

        $this->assertSame(35, $result->getOrigin()->getBalance());
        $this->assertSame('300', $result->getDestination()->getId());
        $this->assertSame(15, $result->getDestination()->getBalance());
    }

    public function testTransferFromNonExistingAccountThrowsException(): void
    {
        $this->expectException(AccountNotFoundException::class);

        $this->service->transfer('999', '300', 10);
    }

    public function testGetBalanceReturnsBalance(): void
    {
        $this->service->deposit('100', 42);

        $balance = $this->service->getBalance('100');

        $this->assertSame(42, $balance);
    }

    public function testGetBalanceForNonExistingAccountThrowsException(): void
    {
        $this->expectException(AccountNotFoundException::class);

        $this->service->getBalance('999');
    }

    public function testResetClearsAllAccounts(): void
    {
        $this->service->deposit('100', 50);
        $this->service->deposit('200', 30);

        $this->service->reset();

        $this->expectException(AccountNotFoundException::class);
        $this->service->getBalance('100');
    }
}
