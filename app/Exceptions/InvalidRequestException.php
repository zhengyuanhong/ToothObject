<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Throwable;

class InvalidRequestException extends Exception
{
    public function __construct($message = "", $code = 201, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return response()->json([
            'code' => $this->code,
            'message' => $this->message,
            'data' => [],
            'time' => time()
        ]);
    }
}
