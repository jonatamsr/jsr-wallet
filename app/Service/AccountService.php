<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\AccountNotFoundException;
use App\Model\Account;
use App\Model\TransferResult;
use App\Repository\AccountRepository;

class AccountService
{
    public function __construct(
        private readonly AccountRepository $repository,
    ) {}

    public function deposit(string $destination, int $amount): Account
    {
        $account = $this->repository->find($destination);

        if ($account === null) {
            $account = new Account($destination);
        }

        $account->deposit($amount);
        $this->repository->save($account);

        return $account;
    }

    public function withdraw(string $origin, int $amount): Account
    {
        $account = $this->repository->find($origin);

        if ($account === null) {
            throw new AccountNotFoundException($origin);
        }

        $account->withdraw($amount);
        $this->repository->save($account);

        return $account;
    }

    public function transfer(string $origin, string $destination, int $amount): TransferResult
    {
        $originAccount = $this->repository->find($origin);

        if ($originAccount === null) {
            throw new AccountNotFoundException($origin);
        }

        $destinationAccount = $this->repository->find($destination);

        if ($destinationAccount === null) {
            $destinationAccount = new Account($destination);
        }

        $originAccount->withdraw($amount);
        $destinationAccount->deposit($amount);

        $this->repository->save($originAccount);
        $this->repository->save($destinationAccount);

        return new TransferResult($originAccount, $destinationAccount);
    }

    public function getBalance(string $accountId): int
    {
        $account = $this->repository->find($accountId);

        if ($account === null) {
            throw new AccountNotFoundException($accountId);
        }

        return $account->getBalance();
    }

    public function reset(): void
    {
        $this->repository->reset();
    }
}
