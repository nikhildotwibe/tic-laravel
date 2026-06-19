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
                'username' => 'testuser',
                'first_name' => 'Super Admin',
                'email' => 'testuser@tictours.com',
                'password' => Hash::make('test@12'),
            ],
        ];

        $user = User::where('username', 'testuser')->first();
        
        if ($user) {
            // Update the password explicitly since $fillable is empty
            $user->password = Hash::make('test@12');
            $user->save();
        } else {
            // Insert the testuser if it doesn't exist
            User::insert($users);
            $role = new UsersRole();
            $role->id = Str::uuid()->toString();
            $role->role_id = Role::where('slug', 'super-admin')->first()->id;
            $role->user_id = User::where('username', 'testuser')->first()->id;
            $role->save();
        }
    }
}
