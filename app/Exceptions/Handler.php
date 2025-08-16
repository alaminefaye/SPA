<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

        // Personnalisation de la gestion des erreurs d'autorisation
        $this->renderable(function (AuthorizationException $e, $request) {
            return response()->view('errors.403', [], 403);
        });

        // Gestion des exceptions AccessDeniedHttpException (403)
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            return response()->view('errors.403', [], 403);
        });
    }
}
