<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HandleNonInertiaRedirects;
use App\Http\Middleware\SetLocaleFromPreference;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocaleFromPreference::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            HandleNonInertiaRedirects::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response) {
            $statusCode = $response->getStatusCode();

            if ($statusCode === 419) {
                Inertia::flash('toast', [
                    'type' => 'error',
                    'message' => 'The page expired, please try again.',
                ]);

                return back();
            }

            if (! request()->expectsJson() && in_array($statusCode, [403, 404, 500, 503], true)) {
                return Inertia::render('error', [
                    'status' => $statusCode,
                ])
                    ->toResponse(request())
                    ->setStatusCode($statusCode);
            }

            return $response;
        });
    })->create();
