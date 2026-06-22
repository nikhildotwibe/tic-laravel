<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Add base_amount and base_markup to itinerary_entries.
 *
 * base_amount  — the canonical TOTAL cost for this entry (regardless of PER/TOTAL price mode).
 *                The frontend divides this by person-count when displaying in PER mode.
 *                Persisting it here prevents the double-division bug on page refresh.
 *
 * base_markup  — the canonical TOTAL markup for this entry, same reasoning.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            // Stored after `amount` so they sit together logically
            $table->double('base_amount')->nullable()->after('amount');
            $table->double('base_markup')->default(0)->after('base_amount');
        });
    }

    public function down(): void
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->dropColumn(['base_amount', 'base_markup']);
        });
    }
};
