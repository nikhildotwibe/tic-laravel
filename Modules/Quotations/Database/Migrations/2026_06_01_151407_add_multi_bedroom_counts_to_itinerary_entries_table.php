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
            $table->integer('two_bedroom_count')->nullable()->after('triple_count');
            $table->integer('three_bedroom_count')->nullable()->after('two_bedroom_count');
            $table->integer('four_bedroom_count')->nullable()->after('three_bedroom_count');
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
            $table->dropColumn([
                'two_bedroom_count',
                'three_bedroom_count',
                'four_bedroom_count'
            ]);
        });
    }
};
