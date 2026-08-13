<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\NotificationSetting;

class NotificationSettingController extends Controller
{
    /**
     * Fetch notification settings (defaults to enquiry_confirmed event).
     */
    public function index(Request $request)
    {
        $eventKey = $request->query('event_key', 'enquiry_confirmed');
        $setting = NotificationSetting::where('event_key', $eventKey)->first();

        if (!$setting) {
            return response()->json([
                'success' => true,
                'data' => [
                    'event_key' => $eventKey,
                    'roles' => [],
                    'user_ids' => [],
                    'is_active' => true,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Store or update notification settings.
     */
    public function store(Request $request)
    {
        $eventKey = $request->input('event_key', 'enquiry_confirmed');
        $roles = $request->input('roles', []);
        $userIds = $request->input('user_ids', []);
        $isActive = $request->has('is_active') ? (bool)$request->input('is_active') : true;

        $setting = NotificationSetting::updateOrCreate(
            ['event_key' => $eventKey],
            [
                'roles' => is_array($roles) ? $roles : [],
                'user_ids' => is_array($userIds) ? $userIds : [],
                'is_active' => $isActive,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification settings saved successfully',
            'data' => $setting
        ]);
    }
}
