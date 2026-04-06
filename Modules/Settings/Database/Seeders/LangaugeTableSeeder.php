<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Settings\Entities\Language;

class LangaugeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $languages = [
            [
                'id' => Str::uuid()->toString(),
                'language' => 'English',
                'slug' => 'en'
            ],
            [
                'id' => Str::uuid()->toString(),
                'language' => 'Arabic',
                'slug' => 'ar'
            ]
        ];

        if (!Language::exists()) {
            Language::insert($languages);
        }
    }
}
