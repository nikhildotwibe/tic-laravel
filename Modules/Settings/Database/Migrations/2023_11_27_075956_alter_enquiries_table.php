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
            $table->dropColumn('priority');
            $table->char('priority_id', 36)->after('lead_source_id');
            $table->foreign(['priority_id'])->references(['id'])->on('priorities')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
        });


        Schema::table('enquiries', function (Blueprint $table) {
            $table->char('agent_id', 36)->nullable()->after('type');
            $table->foreign(['agent_id'])->references(['id'])->on('agents')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
            $table->string('priority')->after('lead_source_id');
            $table->dropForeign(['priority_id']);
            $table->dropColumn('priority_id');
        });
    }
};
