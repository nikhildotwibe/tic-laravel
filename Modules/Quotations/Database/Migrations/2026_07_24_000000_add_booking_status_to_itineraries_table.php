<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds booking_status to itineraries table.
     * Values: 'pending' | 'confirmed' | 'cancelled'
     * Safe to run even if columns already exist.
     */
    public function up(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('itineraries', 'booking_status')) {
                $table->string('booking_status', 20)->default('pending')->after('is_current');
            }
            if (!Schema::hasColumn('itineraries', 'booking_status_updated_at')) {
                $table->timestamp('booking_status_updated_at')->nullable()->after('booking_status');
            }
            if (!Schema::hasColumn('itineraries', 'booking_status_updated_by')) {
                $table->uuid('booking_status_updated_by')->nullable()->after('booking_status_updated_at');
            }
            if (!Schema::hasColumn('itineraries', 'tour_acknowledgement_data')) {
                $table->json('tour_acknowledgement_data')->nullable()->after('booking_status_updated_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $cols = array_filter([
                Schema::hasColumn('itineraries', 'booking_status_updated_by') ? 'booking_status_updated_by' : null,
                Schema::hasColumn('itineraries', 'booking_status_updated_at') ? 'booking_status_updated_at' : null,
                Schema::hasColumn('itineraries', 'booking_status') ? 'booking_status' : null,
            ]);
            if ($cols) {
                $table->dropColumn(array_values($cols));
            }
        });
    }
};
