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
        Schema::table('rooms', function (Blueprint $table) {
            $table->double('two_bedroom_amount')->nullable()->after('quad_bed_amount');
            $table->double('three_bedroom_amount')->nullable()->after('two_bedroom_amount');
            $table->double('four_bedroom_amount')->nullable()->after('three_bedroom_amount');
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
            $table->dropColumn([
                'two_bedroom_amount',
                'three_bedroom_amount',
                'four_bedroom_amount'
            ]);
        });
    }
};
