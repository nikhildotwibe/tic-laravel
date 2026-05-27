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
            $table->integer('quad_count')->default(0)->after('triple_count');
            $table->integer('two_b_count')->default(0)->after('quad_count');
            $table->integer('three_b_count')->default(0)->after('two_b_count');
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
            $table->dropColumn(['quad_count', 'two_b_count', 'three_b_count']);
        });
    }
};
