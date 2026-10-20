<?php

namespace App\Models;

use Database\Factories\NotificationPolicyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPolicy extends Model
{
    /** @use HasFactory<NotificationPolicyFactory> */
    use HasFactory;

    protected $fillable = ['name', 'level', 'channels', 'recipient_scope', 'retry_interval_seconds', 'max_retries', 'is_active'];

    protected function casts(): array
    {
        return ['channels' => 'array', 'recipient_scope' => 'array', 'retry_interval_seconds' => 'integer', 'max_retries' => 'integer', 'is_active' => 'boolean'];
    }
}
