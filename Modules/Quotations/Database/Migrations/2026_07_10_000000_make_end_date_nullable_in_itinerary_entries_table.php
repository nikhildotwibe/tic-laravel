<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->date('end_date')->nullable()->change();
            $table->time('end_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->date('end_date')->nullable(false)->change();
            $table->time('end_time')->nullable(false)->change();
        });
    }
};
