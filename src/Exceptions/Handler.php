<?php

namespace Local\CMS\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Local\CMS\Traits\Helpers;
use Throwable;

class Handler extends ExceptionHandler
{

    use Helpers;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $json = $this->__expectsJson();

        if (is_a($exception, TokenMismatchException::class)) {
            return $json
                ? $this->__apiFailed($exception->getMessage(), null, 419)
                : abort(419);
        }
        if (is_a($exception, MethodNotAllowedHttpException::class)) {
            return $json
                ? $this->__apiMethodNotAllowed($exception->getMessage())
                : abort(500);
        }
        if (is_a($exception, NotFoundHttpException::class)) {
            if ($json) {
                return $this->__apiNotFound("404 Not Found.");
            }
        }

        if (is_a($exception, ModelNotFoundException::class)) {
            if ($json) {
                return $this->__apiNotFound("Model Not Found.");
            }
        }

        if (is_a($exception, ThrottleRequestsException::class)) {
            if ($json) {
                return $this->__apiFailed("Server Timeout. Please try again later.", null, 429);
            }
        }

        if (is_a($exception, FatalErrorException::class)) {
            if ($json) {
                return $this->__apiFailed("Something went wrong.", null, 500);
            }
        }

        return parent::render($request, $exception);
    }
}
