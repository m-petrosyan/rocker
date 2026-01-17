<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsRepository
{
    public static function getEventCreationStats(int $months = 6): array
    {
        return self::getMonthlyStats(Event::query(), $months, 'created_at');
    }

    public static function getUserActivityStats(int $months = 6): array
    {
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $stats = User::select(
            DB::raw("DATE_FORMAT(COALESCE(last_activity, created_at), '%Y-%m') as month"),
            DB::raw('count(*) as count')
        )
            ->where(function ($query) use ($startDate) {
                $query->where('last_activity', '>=', $startDate)
                    ->orWhere(function ($q) use ($startDate) {
                        $q->whereNull('last_activity')
                            ->where('created_at', '>=', $startDate);
                    });
            })
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return self::formatStatsData($stats, $months, $startDate);
    }

    public static function getDiskStats(): array
    {
        $path = base_path();
        $free = disk_free_space($path);
        
        // Calculate project size using shell command for speed, fallback to recursive iterator
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

        return [
            'free' => self::formatBytes($free),
            'project' => self::formatBytes($projectSize),
        ];
    }

    protected static function getDirectorySize($path): int
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
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

        return round($bytes, $precision) . ' ' . $units[$pow];
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
}