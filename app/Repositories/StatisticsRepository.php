<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsRepository
{
    public static function getUserRegistrationsStats(int $months = 6): array
    {
        return self::getMonthlyStats(User::query(), $months);
    }

    public static function getEventCreationStats(int $months = 6): array
    {
        return self::getMonthlyStats(Event::query(), $months);
    }

    protected static function getMonthlyStats($query, int $months): array
    {
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $stats = $query->select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

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
