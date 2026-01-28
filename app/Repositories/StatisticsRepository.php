<?php

namespace App\Repositories;

use App\Enums\CountryEnum;
use App\Models\Event;
use App\Models\User;
use App\Models\UserSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatisticsRepository
{
    public static function getEventCreationStats(int $months = 6): array
    {
        return self::getMonthlyStats(Event::query(), $months, 'created_at');
    }

    public static function getUserActivityStats(int $months = 6): array
    {
        return Cache::store('redis')->remember(
            "user_activity_stats_{$months}",
            now()->addHours(6),
            function () use ($months) {
                $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

                $users = User::query()
                    ->where(function ($query) use ($startDate) {
                        $query->where('last_activity', '>=', $startDate)
                            ->orWhere(function ($q) use ($startDate) {
                                $q->whereNull('last_activity')
                                    ->where('created_at', '>=', $startDate);
                            });
                    })
                    ->get(['last_activity', 'created_at']);

                $stats = $users
                    ->groupBy(function ($user) {
                        return Carbon::parse(
                            $user->last_activity ?? $user->created_at
                        )->format('Y-m');
                    })
                    ->map(fn($group) => $group->count())
                    ->toArray();

                return self::formatStatsData($stats, $months, $startDate);
            }
        );
    }

    public static function getDiskStats(): array
    {
        return Cache::store('redis')->remember('disk_stats', now()->addHours(1), function () {
            $path = base_path();
            $free = disk_free_space($path);
            $total = disk_total_space($path);
            $used = $total - $free;

            $projectSize = 0;
            if (function_exists('shell_exec') && !str_contains(ini_get('disable_functions'), 'shell_exec')) {
                $output = shell_exec("du -sb $path");
                if ($output) {
                    $projectSize = (int)explode("\t", $output)[0];
                }
            }

            if ($projectSize === 0) {
                $projectSize = self::getDirectorySize($path);
            }

            $backupPath = storage_path('app/private/Rocker.am');
            $backupSize = is_dir($backupPath) ? self::getDirectorySize($backupPath) : 0;

            $percentProject = ($projectSize / $total) * 100;
            $percentBackup = ($backupSize / $total) * 100;
            $percentUsed = ($used / $total) * 100;

            return [
                'free' => self::formatBytes($free),
                'total' => self::formatBytes($total),
                'used' => self::formatBytes($used),
                'project' => self::formatBytes($projectSize),
                'backups' => self::formatBytes($backupSize),
                'percent_free' => round(($free / $total) * 100, 1),
                'percent_used' => max(round($percentUsed, 1), $used > 0 ? 0.1 : 0),
                'percent_project' => round($percentProject, 1),
                'percent_backup' => round($percentBackup, 1),
            ];
        });
    }

    protected static function getDirectorySize($path): int
    {
        $size = 0;
        foreach (
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
            ) as $file
        ) {
            $size += $file->getSize();
        }

        return $size;
    }

    protected static function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    protected static function getMonthlyStats($query, int $months, string $column = 'created_at'): array
    {
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $stats = $query->select(
            DB::raw("DATE_FORMAT($column, '%Y-%m') as month"),
            DB::raw('count(*) as count')
        )
            ->where($column, '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return self::formatStatsData($stats, $months, $startDate);
    }

    protected static function formatStatsData(array $stats, int $months, Carbon $startDate): array
    {
        $labels = [];
        $data = [];

        for ($i = 0; $i < $months; $i++) {
            $month = $startDate->copy()->addMonths($i)->format('Y-m');
            $labels[] = $startDate->copy()->addMonths($i)->translatedFormat('M Y');
            $data[] = $stats[$month] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public static function getUserCountryStats(): array
    {
        return [
            'am' => UserSettings::where('country', CountryEnum::ARMENIA->value)->count(),
            'ge' => UserSettings::where('country', CountryEnum::GEORGIA->value)->count(),
            'all' => UserSettings::where('country', CountryEnum::ALL->value)->count(),
        ];
    }
}