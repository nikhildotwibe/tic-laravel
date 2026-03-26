<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Entities\ActivityType;

class ActivityTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ['Meals', 'Excursion', 'Transfer', 'Entrance Fee', 'Guide Service'];

        foreach ($types as $type) {
            ActivityType::updateOrCreate(['name' => $type]);
        }
    }
}
