<?php

namespace App\Models;

use Database\Factories\NotificationLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    /** @use HasFactory<NotificationLogFactory> */
    use HasFactory;

    protected $fillable = ['warning_id', 'contact_id', 'channel', 'status', 'provider_message_id', 'attempt', 'sent_at', 'delivered_at', 'duration_seconds', 'error_message'];

    protected function casts(): array
    {
        return ['attempt' => 'integer', 'sent_at' => 'datetime', 'delivered_at' => 'datetime', 'duration_seconds' => 'integer'];
    }
}
