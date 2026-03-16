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
        Schema::table('hotel_amenities', function (Blueprint $table) {
            $table->dropColumn('amenity');
            $table->char('name')->after('seq');
        });

        Schema::table('room_amenities', function (Blueprint $table) {
            $table->dropColumn('amenity');
            $table->char('name')->after('seq');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotel_amenities', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->char('amenity')->after('seq');
        });

        Schema::table('room_amenities', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->char('amenity')->after('seq');
        });
    }
};
