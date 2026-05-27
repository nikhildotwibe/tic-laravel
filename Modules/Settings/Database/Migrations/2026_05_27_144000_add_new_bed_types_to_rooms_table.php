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
            $table->boolean('is_quad_bed_available')->default(false)->after('child_n_bed_amount');
            $table->double('quad_bed_amount')->default(0)->after('is_quad_bed_available');
            $table->boolean('is_two_b_available')->default(false)->after('quad_bed_amount');
            $table->double('two_b_amount')->default(0)->after('is_two_b_available');
            $table->boolean('is_three_b_available')->default(false)->after('two_b_amount');
            $table->double('three_b_amount')->default(0)->after('is_three_b_available');
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
                'is_quad_bed_available', 'quad_bed_amount',
                'is_two_b_available', 'two_b_amount',
                'is_three_b_available', 'three_b_amount'
            ]);
        });
    }
};
