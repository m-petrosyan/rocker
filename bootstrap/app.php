<?php

use App\Http\Middleware\CheckUserBlocked;
use App\Http\Middleware\EnsureEmailVerifiedIfPresent;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LastActivityMiddleware;
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
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            CheckUserBlocked::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
        $middleware->alias([
            'email.verified.if.present' => EnsureEmailVerifiedIfPresent::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'activity' => LastActivityMiddleware::class,
            'blocked' => CheckUserBlocked::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('disk:check')->daily()->at('18:30');
        $schedule->command('app:sitemap')->daily()->at('19:00');

        $schedule->exec('rm -rf '.storage_path('app/backup-temp'))
            ->daily()
            ->at('19:25')
            ->description('Cleanup stale backup temp directory before backup');

        $schedule->command('backup:run')->daily()->at('19:30');
        $schedule->command('app:old-event-messages-delete')->weekly();

        $schedule->command('backup:clean')->daily()->at('20:30');

        $schedule->exec('find '.storage_path('app/public/tmp').' -type f -mtime +1 -delete && find '.storage_path('app/public/tmp').' -type d -empty -delete')
            ->daily()
            ->at('03:00')
            ->description('Cleanup stale uploaded temp files and empty directories older than 1 day');

        $schedule->command('app:fetch-facebook-events')->dailyAt('06:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if (! app()->environment('local')) {
            Integration::handles($exceptions);
        }
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return Inertia::render('404')
                ->toResponse($request)
                ->setStatusCode(404);
        });
    })->create();
