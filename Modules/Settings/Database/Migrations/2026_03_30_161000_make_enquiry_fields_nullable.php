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
        Schema::table('enquiries', function (Blueprint $table) {
            // Making lead_source_id nullable
            if (Schema::hasColumn('enquiries', 'lead_source_id')) {
                $table->char('lead_source_id', 36)->nullable()->change();
            }
            // Making priority_id nullable (depending on what it's named in the db)
            if (Schema::hasColumn('enquiries', 'priority_id')) {
                $table->char('priority_id', 36)->nullable()->change();
            }
            if (Schema::hasColumn('enquiries', 'priority')) {
                $table->string('priority')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (Schema::hasColumn('enquiries', 'lead_source_id')) {
                $table->char('lead_source_id', 36)->nullable(false)->change();
            }
            if (Schema::hasColumn('enquiries', 'priority_id')) {
                $table->char('priority_id', 36)->nullable(false)->change();
            }
            if (Schema::hasColumn('enquiries', 'priority')) {
                $table->string('priority')->nullable(false)->change();
            }
        });
    }
};
