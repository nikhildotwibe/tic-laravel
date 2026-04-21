<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('symbol')->nullable();
            $table->string('code')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->string('currency_format')->nullable();
            $table->string('from_currency')->nullable();
            $table->string('to_currency')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn([
                'symbol', 
                'code', 
                'exchange_rate', 
                'currency_format', 
                'from_currency', 
                'to_currency'
            ]);
        });
    }
};
