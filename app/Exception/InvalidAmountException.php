<?php

declare(strict_types=1);

namespace App\Exception;

class InvalidAmountException extends \RuntimeException
{
    public function __construct(int $amount)
    {
        parent::__construct("Invalid amount: {$amount}. Must be positive.");
    }
}
