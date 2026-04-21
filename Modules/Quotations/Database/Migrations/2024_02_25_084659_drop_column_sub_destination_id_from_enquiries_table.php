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
            $table->dropForeign(['sub_destination_id']);
            $table->dropColumn('sub_destination_id');
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
            $table->char('sub_destination_id', 36)->nullable()->after('destination_id');
            $table->foreign(['sub_destination_id'])->references(['id'])->on('sub_destinations')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }
};
