<?php

namespace App\Infrastructure\Exception;

use Exception;

class UniqueConstraintViolationException extends Exception
{
    public function __construct(string $message = "Unique constraint violation", int $code = 409, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}