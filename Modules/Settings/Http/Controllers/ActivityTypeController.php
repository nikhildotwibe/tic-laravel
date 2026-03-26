<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Modules\Settings\Entities\ActivityType;

class ActivityTypeController extends BaseController
{
    public function index()
    {
        try {
            $types = ActivityType::all();
            return $this->sendResponse($types, 'Activity Types Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
