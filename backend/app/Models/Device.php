<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'type', 'district', 'latitude', 'longitude', 'status', 'last_seen_at'];

    protected function casts(): array
    {
        return ['latitude' => 'decimal:6', 'longitude' => 'decimal:6', 'last_seen_at' => 'datetime'];
    }
}
