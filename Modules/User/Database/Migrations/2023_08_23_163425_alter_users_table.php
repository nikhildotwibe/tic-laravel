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
        Schema::table('users', function (Blueprint $table) {
            $table->char('language', 36)->nullable()->after('address')->index('users_language_foreign');
            $table->char('country', 36)->nullable()->after('language')->index('users_country_foreign');

            $table->foreign(['language'])->references(['id'])->on('languages')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['country'])->references(['id'])->on('countries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['language']);
            $table->dropColumn('username');

            $table->dropForeign(['country']);
            $table->dropColumn('country');
        });
    }
};
