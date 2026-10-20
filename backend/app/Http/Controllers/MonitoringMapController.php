<?php

namespace App\Http\Controllers;

use App\Models\HazardInstallationPlan;
use Illuminate\Http\JsonResponse;

class MonitoringMapController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $points = HazardInstallationPlan::query()
            ->with(['hazardPoint', 'plannedDevices.device'])
            ->whereNotNull('longitude')->whereNotNull('latitude')
            ->orderBy('source_row')->get()
            ->map(fn (HazardInstallationPlan $plan): array => [
                'id' => $plan->hazard_point_id,
                'code' => $plan->hazardPoint?->code,
                'name' => $plan->point_name,
                'hazard_type' => $plan->hazard_type,
                'district' => $plan->hazardPoint?->district,
                'level' => $plan->hazardPoint?->level,
                'longitude' => (float) $plan->longitude,
                'latitude' => (float) $plan->latitude,
                'notes' => $plan->field_notes,
                'devices' => $plan->plannedDevices->filter(fn ($item): bool => $item->device !== null)->map(fn ($item): array => [
                    'id' => $item->device->id, 'code' => $item->device->code, 'name' => $item->device->name,
                    'type' => $item->device->type, 'status' => $item->device->status,
                    'longitude' => (float) $item->device->longitude, 'latitude' => (float) $item->device->latitude,
                ])->values()->all(),
            ])->values();

        return response()->json([
            'center' => ['longitude' => 109.8403, 'latitude' => 40.6574],
            'tianditu_key' => config('services.tianditu.key'),
            'source' => '隐患点设备安装表.xlsx',
            'points' => $points,
            'summary' => [
                'points' => $points->count(),
                'devices' => $points->sum(fn (array $point): int => count($point['devices'])),
                'online' => $points->sum(fn (array $point): int => collect($point['devices'])->where('status', 'online')->count()),
            ],
        ]);
    }
}
