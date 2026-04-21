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
            $table->char('customer_id', 36)->nullable()->after('agent_id');
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->char('assigned_to', 36)->nullable()->after('priority_id');
            $table->foreign(['assigned_to'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
        });
    }
};
