<?php

namespace App\Models;

use Database\Factories\RiskZoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskZone extends Model
{
    /** @use HasFactory<RiskZoneFactory> */
    use HasFactory;

    protected $fillable = ['code', 'name', 'level', 'district', 'area_sq_km', 'threatened_people', 'threatened_property', 'description', 'status'];

    protected function casts(): array
    {
        return ['area_sq_km' => 'decimal:4', 'threatened_people' => 'integer', 'threatened_property' => 'decimal:2'];
    }
}
