<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Module;
use Modules\User\Entities\Permission;
use Illuminate\Support\Str;
use Modules\User\Entities\RolesPermission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $permissionKeys = [
            [
                'name' => 'All',
                'slug' => '-read-all',
            ],
            [
                'name' => 'Added',
                'slug' => '-read-added',
            ],
            [
                'name' => 'Assigned',
                'slug' => '-read-assigned',
            ],
            [
                'name' => 'Added and Assigned',
                'slug' => '-read-added-and-assigned',
            ],
            [
                'name' => 'All',
                'slug' => '-write-all',
            ],
            [
                'name' => 'Added',
                'slug' => '-write-added',
            ],
            [
                'name' => 'Assigned',
                'slug' => '-write-assigned',
            ],
            [
                'name' => 'Added and Assigned',
                'slug' => '-write-added-and-assigned',
            ],
            [
                'name' => 'All',
                'slug' => '-update-all',
            ],
            [
                'name' => 'Added',
                'slug' => '-update-added',
            ],
            [
                'name' => 'Assigned',
                'slug' => '-update-assigned',
            ],
            [
                'name' => 'Added and Assigned',
                'slug' => '-update-added-and-assigned',
            ],
            [
                'name' => 'All',
                'slug' => '-delete-all',
            ],
            [
                'name' => 'Added',
                'slug' => '-delete-added',
            ],
            [
                'name' => 'Assigned',
                'slug' => '-delete-assigned',
            ],
            [
                'name' => 'Added and Assigned',
                'slug' => '-delete-added-and-assigned',
            ],
        ];


        $modules = Module::latest()->get();
        foreach ($modules as $key => $module) {
            $moduleName = $module->name;
            foreach ($permissionKeys as $key2 => $permission) {

                $slug = Str::lower($moduleName) . $permission['slug'];

                $existingPermission = Permission::where('slug', $slug)->first();
                if ($existingPermission) {
                    $existingPermission->update(
                        [
                            'name' => $permission['name'],
                            'slug' => $slug,
                            'module_id' => $module->id
                        ]
                    );
                } else {
                    Permission::create(
                        [
                            'id' => Str::uuid()->toString(),
                            'name' => $permission['name'],
                            'slug' => $slug,
                            'module_id' => $module->id
                        ]
                    );
                }
            }
        }


        // $this->call("OthersTableSeeder");
    }
}
