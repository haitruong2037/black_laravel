<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use App\Exceptions\Api\ValidationException as ValidationHandleException;
use App\Exceptions\Api\BadRequestException;
use App\Exceptions\Api\ResourceNotFoundException;
use App\Exceptions\Api\InternalServerErrorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     *
     * @throws \App\Exceptions\Api\ValidationException
     * @throws \App\Exceptions\Api\ResourceNotFoundException
     * @throws \App\Exceptions\Api\BadRequestException
     * @throws \App\Exceptions\Api\InternalServerErrorException
     */
    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            if ($e instanceof ValidationException) {
                $error = new MessageBag($e->errors());
                throw new ValidationHandleException($error);
            } 
            elseif ($e instanceof ModelNotFoundException 
                || $e instanceof NotFoundHttpException
            ) {
                throw new ResourceNotFoundException;
            }
            elseif (
                $e instanceof ResourceNotFoundException ||
                $e instanceof BadRequestException ||
                $e instanceof InternalServerErrorException
            ) {
                return $e->render($request);
            }
        }
        return parent::render($request, $e);
    }
}
