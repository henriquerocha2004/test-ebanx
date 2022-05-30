<?php

namespace TestEbanx\Domain\Exceptions;

use Exception;
use Throwable;

class InvalidOperationException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}