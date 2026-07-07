<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds optional child_amount column to room_meal_plan_entries.
     * Nullable with default 0 — fully backwards-compatible.
     */
    public function up()
    {
        Schema::table('room_meal_plan_entries', function (Blueprint $table) {
            $table->double('child_amount')->default(0)->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('room_meal_plan_entries', function (Blueprint $table) {
            $table->dropColumn('child_amount');
        });
    }
};
