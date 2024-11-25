<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected $statusCode;

    public function __construct($message = "Validation Error", $statusCode = 422)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
