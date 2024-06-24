<?php

namespace App\Exceptions\Api;

use Exception;

class BadRequestException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  \Exception|null  $previous
     * @return void
     */
    public function __construct($message = "Bad Request!", $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
