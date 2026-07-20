<?php

declare(strict_types=1);

namespace App\Exception;

class AccountNotFoundException extends BusinessException
{
    public function __construct(string $accountId)
    {
        parent::__construct("Account '{$accountId}' not found.");
    }
}
