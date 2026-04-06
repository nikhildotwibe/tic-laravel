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
            $table->dropColumn('name');
            $table->dropColumn('email');
            $table->dropColumn('mobile');
            $table->dropColumn('salute');
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
            if (!Schema::hasColumn('enquiries', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('enquiries', 'email')) {
                $table->string('email');
            }
            if (!Schema::hasColumn('enquiries', 'mobile')) {
                $table->string('mobile');
            }
            if (!Schema::hasColumn('enquiries', 'salute')) {
                $table->string('salute');
            }
        });
    }
};
