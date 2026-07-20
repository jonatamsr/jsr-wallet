<?php

declare(strict_types=1);

namespace App\Exception;

class InsufficientFundsException extends BusinessException
{
    public function __construct(string $accountId, int $balance, int $amount)
    {
        parent::__construct(
            "Account '{$accountId}' has insufficient funds: balance={$balance}, requested={$amount}."
        );
    }
}
