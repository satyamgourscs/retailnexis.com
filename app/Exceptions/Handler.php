<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
     * @throws \Throwable
     */
    public function report(Throwable  $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        try {
            return parent::render($request, $exception);
        } catch (\Throwable $inner) {
            // When bootstrap fails early, View may not be bound yet; parent::render() then throws
            // BindingResolutionException: Target class [view] does not exist — hide the real error.
            if (str_contains($inner->getMessage(), 'Target class [view] does not exist')) {
                return response(
                    "Application error (could not render error page).\n\n"
                    . 'Original: '.$exception->getMessage()."\n"
                    . $exception->getFile().':'.$exception->getLine()."\n\n"
                    . "Fix on server: php artisan optimize:clear && php artisan config:cache\n"
                    . 'Ensure bootstrap/cache is writable and vendor/ is complete (composer install).',
                    500,
                    ['Content-Type' => 'text/plain; charset=UTF-8']
                );
            }

            throw $inner;
        }
    }
}
