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
        Schema::table('itineraries', function (Blueprint $table) {
            $table->double('total_amount')->default(0)->after('price_mode')->comment('Sum of (net + markup) in base currency');
            $table->double('grand_total')->default(0)->after('total_amount')->comment('Final total with taxes and discounts in base currency');
            $table->double('converted_total')->default(0)->after('grand_total')->comment('Final total in target currency');
            $table->double('exchange_rate')->default(1)->after('converted_total')->comment('Rate used for conversion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'grand_total', 'converted_total', 'exchange_rate']);
        });
    }
};
