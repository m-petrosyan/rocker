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