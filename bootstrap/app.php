<?php

use App\Http\Middleware\EnsureEmailVerifiedIfPresent;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Inertia\Inertia;
use Sentry\Laravel\Integration;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
        $middleware->alias([
            'email.verified.if.present' => EnsureEmailVerifiedIfPresent::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('disk:check')->daily()->at('18:30');
        $schedule->command('app:sitemap')->daily()->at('19:00');
        $schedule->command('backup:run')->daily()->at('19:30');
        $schedule->command('backup:clean')->cron('0 23 */14 * *');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if (!app()->environment('local')) {
            Integration::handles($exceptions);
        }
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return Inertia::render('404')
                ->toResponse($request)
                ->setStatusCode(404);
        });
    })->create();
