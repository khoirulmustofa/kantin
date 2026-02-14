<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, Throwable $exception, Request $request) {
        //     if (!app()->environment(['local']) && in_array($response->getStatusCode(), [500, 503, 404])) {
        //         return Inertia::render('Errors/NotFound', [
        //             'status' => $response->getStatusCode()
        //         ])
        //             ->toResponse($request)
        //             ->setStatusCode($response->getStatusCode());
        //     } elseif (in_array($response->getStatusCode(), [500, 503, 404])) {
        //         return Inertia::render('Errors/NotFound', [
        //             'status' => $response->getStatusCode()
        //         ])
        //             ->toResponse($request)
        //             ->setStatusCode($response->getStatusCode());
        //     }

        //     return $response;
        // });
    })->create();
