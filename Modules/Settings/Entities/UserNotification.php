<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'enquiry_id',
        'itinerary_id',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];
}
