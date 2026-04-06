<?php

use Modules\Settings\Entities\SystemSetting;


if (!function_exists('pageLength')) {
    function pageLength()
    {
        return SystemSetting::where('key', 'rows_per_page')->first()->value ?? 10; // Todo : config cache
    }
}
