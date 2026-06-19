<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\User\Entities\User;
use Illuminate\Support\Facades\Hash;

User::where('username', 'super_admin')->update(['password' => Hash::make('test@12')]);
echo "Password updated successfully\n";
