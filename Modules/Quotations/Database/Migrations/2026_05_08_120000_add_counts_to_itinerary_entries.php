<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->integer('adult_count')->default(0)->after('no_of_person');
            $table->integer('child_count')->default(0)->after('adult_count');
        });
    }

    public function down()
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->dropColumn(['adult_count', 'child_count']);
        });
    }
};
