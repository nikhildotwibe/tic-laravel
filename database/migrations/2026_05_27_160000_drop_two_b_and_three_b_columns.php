<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'is_two_b_available',
                'two_b_amount',
                'is_three_b_available',
                'three_b_amount'
            ]);
        });

        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->dropColumn([
                'two_b_count',
                'three_b_count'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('is_two_b_available')->default(false);
            $table->double('two_b_amount')->default(0);
            $table->boolean('is_three_b_available')->default(false);
            $table->double('three_b_amount')->default(0);
        });

        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->integer('two_b_count')->default(0);
            $table->integer('three_b_count')->default(0);
        });
    }
};
