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
     */
    public function up(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->string('booking_status', 20)->default('pending')->after('is_current');
            $table->timestamp('booking_status_updated_at')->nullable()->after('booking_status');
            $table->foreignId('booking_status_updated_by')->nullable()->constrained('users')->nullOnDelete()->after('booking_status_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropColumn(['booking_status', 'booking_status_updated_at', 'booking_status_updated_by']);
        });
    }
};
