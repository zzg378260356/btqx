<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\WeatherWarning;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $warnings = WeatherWarning::query()->where('status', '!=', '已完成')->latest('published_at')->get();
        $counts = collect(['red', 'orange', 'yellow', 'blue'])->mapWithKeys(fn (string $level): array => [$level => $warnings->where('level', $level)->count()]);
        $dates = collect(range(6, 0))->map(fn (int $days) => now()->subDays($days));
        $recent = WeatherWarning::query()->where('published_at', '>=', now()->subDays(6)->startOfDay())->get();
        $series = collect(['red', 'orange', 'yellow', 'blue'])->mapWithKeys(fn (string $level): array => [$level => $dates->map(fn ($date) => $recent->filter(fn ($warning) => $warning->level === $level && $warning->published_at->isSameDay($date))->count())->values()]);

        return response()->json(['demo' => true, 'counts' => $counts, 'devices' => ['total' => Device::count(), 'online' => Device::where('status', 'online')->count()], 'warnings' => $warnings->map(fn ($w) => ['id' => $w->id, 'title' => $w->title, 'level' => $w->level, 'district' => $w->district, 'status' => $w->status, 'published_at' => $w->published_at->format('Y-m-d H:i'), 'responsible_unit' => $w->responsible_unit]), 'trend' => ['dates' => $dates->map(fn ($d) => $d->format('m-d'))->values(), 'series' => $series], 'types' => $warnings->groupBy('type')->map(fn ($items, $type) => ['name' => $type, 'value' => $items->count()])->values()]);
    }
}
