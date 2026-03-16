<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Settings\Entities\Country;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $countries = [
            [
                'id' => Str::uuid()->toString(),
                'name' => 'India',
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'UAE',
            ]
        ];

        if (!Country::exists()) {
            Country::insert($countries);
        }
    }
}
