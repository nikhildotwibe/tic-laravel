<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\Role;
use Modules\User\Entities\UsersRole;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $users = [
            [
                'id' => Str::uuid()->toString(),
                'username' => 'super_admin',
                'first_name' => 'Super Admin',
                'email' => 'super-admin@tictours.com',
                'password' => Hash::make('123456'),
            ],
        ];

        if (!User::exists()) {
            User::insert($users);
            $role = new UsersRole();
            $role->id = Str::uuid()->toString();
            $role->role_id = Role::where('slug', 'super-admin')->first()->id;
            $role->user_id = User::first()->id;
            $role->save();
        }
    }
}
