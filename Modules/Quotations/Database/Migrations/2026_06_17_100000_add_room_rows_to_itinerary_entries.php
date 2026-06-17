<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->json('room_rows')->nullable()->after('four_bedroom_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->dropColumn(['room_rows']);
        });
    }
};
