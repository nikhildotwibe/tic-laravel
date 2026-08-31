<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\User\Entities\Module;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $modules = [
            [
                'name' => 'Dashboard',
            ],
            [
                'name' => 'Leads',
            ],
            [
                'name' => 'Enquiry',
            ],
            [
                'name' => 'Reports',
            ],
            [
                'name' => 'Settings',
            ],
        ];

        foreach ($modules as $module) {
            Module::firstOrCreate(
                ['name' => $module['name']]
            );
        }
        // $this->call("OthersTableSeeder");
    }
}
