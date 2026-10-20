<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherWarning extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'level', 'type', 'district', 'status', 'published_at', 'responsible_unit'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
}
