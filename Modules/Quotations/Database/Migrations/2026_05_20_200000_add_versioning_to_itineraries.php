<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds version-tracking columns to the itineraries table so that
     * editing a quotation preserves the previous version as a historical
     * snapshot while creating a new "current" version.
     */
    public function up()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->char('parent_itinerary_id', 36)->nullable()->after('enquiry_id')
                  ->comment('Groups all versions of the same quotation together');
            $table->unsignedInteger('version')->default(1)->after('parent_itinerary_id')
                  ->comment('Auto-incrementing version number within a parent group');
            $table->boolean('is_current')->default(true)->after('version')
                  ->comment('true = active version, false = historical snapshot');

            $table->index(['enquiry_id', 'parent_itinerary_id', 'is_current'], 'idx_itinerary_versioning');
        });

        // Back-fill existing itineraries: each becomes v1, current, its own parent
        \Illuminate\Support\Facades\DB::statement('UPDATE itineraries SET parent_itinerary_id = id, version = 1, is_current = 1 WHERE parent_itinerary_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropIndex('idx_itinerary_versioning');
            $table->dropColumn(['parent_itinerary_id', 'version', 'is_current']);
        });
    }
};
