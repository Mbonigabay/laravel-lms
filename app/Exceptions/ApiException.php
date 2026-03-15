<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    protected $code;

    public function __construct(string $message = "An error occurred", int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->code = $code;
    }
}
