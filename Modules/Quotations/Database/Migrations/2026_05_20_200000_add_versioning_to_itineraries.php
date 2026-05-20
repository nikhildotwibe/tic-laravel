<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->char('parent_itinerary_id', 36)->nullable()->after('enquiry_id');
            $table->unsignedInteger('version')->default(1)->after('parent_itinerary_id');
            $table->boolean('is_current')->default(true)->after('version');

            $table->index(['enquiry_id', 'parent_itinerary_id', 'is_current'], 'idx_itinerary_versioning');
        });

        // Backfill: set parent_itinerary_id = own id for all existing records
        DB::statement("UPDATE itineraries SET parent_itinerary_id = id WHERE parent_itinerary_id IS NULL");
    }

    public function down()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropIndex('idx_itinerary_versioning');
            $table->dropColumn(['parent_itinerary_id', 'version', 'is_current']);
        });
    }
};
