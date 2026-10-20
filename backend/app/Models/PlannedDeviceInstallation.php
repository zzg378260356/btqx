<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlannedDeviceInstallation extends Model
{
    public const TYPES = [
        'weather_station',
        'gnss',
        'beidou_gnss',
        'rain_gauge',
        'flow_meter',
        'camera',
    ];

    protected $fillable = [
        'hazard_installation_plan_id',
        'device_id',
        'device_type',
        'sequence',
        'installation_position',
        'longitude',
        'latitude',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'longitude' => 'decimal:8',
            'latitude' => 'decimal:8',
        ];
    }

    public function installationPlan(): BelongsTo
    {
        return $this->belongsTo(HazardInstallationPlan::class, 'hazard_installation_plan_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
