<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HazardInstallationPlan extends Model
{
    protected $fillable = [
        'hazard_point_id',
        'source_document',
        'source_row',
        'source_code',
        'point_name',
        'hazard_type',
        'longitude',
        'latitude',
        'layout_notes',
        'field_notes',
        'verification_status',
    ];

    protected function casts(): array
    {
        return [
            'source_row' => 'integer',
            'longitude' => 'decimal:8',
            'latitude' => 'decimal:8',
        ];
    }

    public function hazardPoint(): BelongsTo
    {
        return $this->belongsTo(HazardPoint::class);
    }

    public function plannedDevices(): HasMany
    {
        return $this->hasMany(PlannedDeviceInstallation::class);
    }
}
