<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidAmountException extends BusinessException
{
    public function __construct(int $amount)
    {
        parent::__construct("Invalid amount: {$amount}. Must be positive.");
    }

    public function getStatusCode(): int
    {
        return 422;
    }
}
