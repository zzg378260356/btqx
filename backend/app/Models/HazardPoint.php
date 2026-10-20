<?php

namespace App\Models;

use Database\Factories\HazardPointFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HazardPoint extends Model
{
    /** @use HasFactory<HazardPointFactory> */
    use HasFactory;

    protected $fillable = ['code', 'name', 'hazard_type', 'level', 'district', 'description', 'threatened_people', 'threatened_property', 'latitude', 'longitude', 'status', 'discovered_at', 'closed_at'];

    protected function casts(): array
    {
        return ['threatened_people' => 'integer', 'threatened_property' => 'decimal:2', 'latitude' => 'decimal:6', 'longitude' => 'decimal:6', 'discovered_at' => 'datetime', 'closed_at' => 'datetime'];
    }
}
