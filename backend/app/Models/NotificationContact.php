<?php

namespace App\Models;

use Database\Factories\NotificationContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationContact extends Model
{
    /** @use HasFactory<NotificationContactFactory> */
    use HasFactory;

    protected $fillable = ['name', 'organization', 'district', 'phone', 'email', 'notification_channels', 'is_active'];

    protected function casts(): array
    {
        return ['notification_channels' => 'array', 'is_active' => 'boolean'];
    }
}
