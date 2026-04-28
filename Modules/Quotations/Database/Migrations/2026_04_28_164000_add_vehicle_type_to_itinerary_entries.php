<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->string('vehicle_type')->nullable()->after('transfer_type');
        });
    }

    public function down()
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->dropColumn('vehicle_type');
        });
    }
};
