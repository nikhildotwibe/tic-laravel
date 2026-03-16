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
                'name' => 'Follow ups',
            ],
            [
                'name' => 'Tickets',
            ],
            [
                'name' => 'Works',
            ],
            [
                'name' => 'Finance',
            ],
            [
                'name' => 'Mails',
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['name' => $module['name']],
                ['id' => Str::uuid()->toString()]
            );
        }
        // $this->call("OthersTableSeeder");
    }
}
