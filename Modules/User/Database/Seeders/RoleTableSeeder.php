<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\User\Entities\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $roles = [
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => '',
                'is_active' => 1,
            ],
        ];

        if (!Role::exists()) {
            Role::insert($roles);
        }
    }
}
