<?php

declare(strict_types=1);

namespace App\Model;

class TransferResult
{
    public function __construct(
        private readonly Account $origin,
        private readonly Account $destination,
    ) {}

    public function getOrigin(): Account
    {
        return $this->origin;
    }

    public function getDestination(): Account
    {
        return $this->destination;
    }
}
