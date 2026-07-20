<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

abstract class BusinessException extends RuntimeException
{
    abstract public function getStatusCode(): int;
}
