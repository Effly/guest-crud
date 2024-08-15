<?php

namespace App\Infrastructure\Exception;

use Exception;

class GuestNotFoundException extends Exception
{
    public function __construct(string $message = "Guest not found", int $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}