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
        Schema::table('activities', function (Blueprint $table) {
            $table->char('activity_type_id', 36)->nullable()->after('sub_destination_id');
            $table->integer('adult_count')->default(0)->after('activity_type_id');
            $table->integer('child_count')->default(0)->after('adult_count');

            $table->foreign(['activity_type_id'])->references(['id'])->on('activity_types')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['activity_type_id']);
            $table->dropColumn(['activity_type_id', 'adult_count', 'child_count']);
        });
    }
};
