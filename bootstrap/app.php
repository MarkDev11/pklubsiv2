<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\DdosProtection;
use App\Http\Middleware\SecurityHeaders;
use App\Models\ErrorLog;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
            | Request::HEADER_X_FORWARDED_PREFIX
            | Request::HEADER_X_FORWARDED_AWS_ELB);

        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        $middleware->append(DdosProtection::class);
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e) {
            try {
                if (! app()->runningInConsole()) {
                    ErrorLog::create([
                        'user_id' => auth()->check() ? auth()->id() : null,
                        'type' => get_class($e),
                        'message' => $e->getMessage() ?: 'System Error',
                        'request_url' => request()->fullUrl(),
                        'payload' => json_encode(request()->except(['password', 'password_confirmation'])),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => mb_substr($e->getTraceAsString(), 0, 5000),
                    ]);
                }
            } catch (Throwable $err) {
            }
        });
    })->create();
