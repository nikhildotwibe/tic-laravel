<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds tour_acknowledgement_data JSON column to itineraries table.
     */
    public function up(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('itineraries', 'tour_acknowledgement_data')) {
                $table->json('tour_acknowledgement_data')->nullable()->after('is_current');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            if (Schema::hasColumn('itineraries', 'tour_acknowledgement_data')) {
                $table->dropColumn('tour_acknowledgement_data');
            }
        });
    }
};
