<?php

declare(strict_types=1);

namespace HyperfTest\Unit;

use App\Model\Account;
use App\Repository\AccountRepository;
use PHPUnit\Framework\TestCase;

class AccountRepositoryTest extends TestCase
{
    private AccountRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new AccountRepository();
        $this->repository->reset();
    }

    public function testFindReturnsNullForNonExistingAccount(): void
    {
        $this->assertNull($this->repository->find('999'));
    }

    public function testSavePersistsAccount(): void
    {
        $account = new Account('100');
        $account->deposit(50);

        $this->repository->save($account);

        $found = $this->repository->find('100');
        $this->assertNotNull($found);
        $this->assertSame('100', $found->getId());
        $this->assertSame(50, $found->getBalance());
    }

    public function testSaveOverwritesExistingAccount(): void
    {
        $account = new Account('100');
        $account->deposit(10);
        $this->repository->save($account);

        $account->deposit(20);
        $this->repository->save($account);

        $found = $this->repository->find('100');
        $this->assertSame(30, $found->getBalance());
    }

    public function testResetClearsAllAccounts(): void
    {
        $account1 = new Account('100');
        $account2 = new Account('200');
        $this->repository->save($account1);
        $this->repository->save($account2);

        $this->repository->reset();

        $this->assertNull($this->repository->find('100'));
        $this->assertNull($this->repository->find('200'));
    }
}
