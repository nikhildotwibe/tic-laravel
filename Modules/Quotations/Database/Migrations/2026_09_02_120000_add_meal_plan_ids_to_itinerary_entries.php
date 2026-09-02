<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->json('meal_plan_ids')->nullable()->after('room_rows');
        });
    }

    public function down()
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->dropColumn('meal_plan_ids');
        });
    }
};
