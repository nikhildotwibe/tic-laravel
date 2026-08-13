<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $table = 'notification_settings';

    protected $fillable = [
        'event_key',
        'roles',
        'user_ids',
        'is_active',
    ];

    protected $casts = [
        'roles' => 'array',
        'user_ids' => 'array',
        'is_active' => 'boolean',
    ];
}
