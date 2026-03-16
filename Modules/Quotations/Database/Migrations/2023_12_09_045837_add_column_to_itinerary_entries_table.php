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
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->char('room_id', 36)->nullable()->after('subject_id');
            $table->foreign(['room_id'])->references(['id'])->on('rooms')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->string('option')->nullable()->after('subject_id');
            $table->double('amount')->default(0)->after('end_time');
            $table->double('markup')->default(0)->after('amount');
        });


        Schema::table('itineraries', function (Blueprint $table) {
            $table->double('extra_markup_percentage')->default(0)->after('valid_until');
            $table->double('extra_markup_amount')->default(0)->after('extra_markup_percentage');
            $table->double('cgst_percentage')->default(0)->after('extra_markup_amount');
            $table->double('sgst_percentage')->default(0)->after('cgst_percentage');
            $table->double('igst_percentage')->default(0)->after('sgst_percentage');
            $table->double('tcs_percentage')->default(0)->after('igst_percentage');
            $table->double('discount_amount')->default(0)->after('tcs_percentage');
            $table->string('currency')->after('discount_amount');
            $table->string('description')->nullable()->after('currency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itinerary_entries', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
            $table->dropColumn('option');
            $table->dropColumn('amount');
            $table->dropColumn('markup');
        });

        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropColumn('extra_markup_percentage');
            $table->dropColumn('extra_markup_amount');
            $table->dropColumn('cgst_percentage');
            $table->dropColumn('sgst_percentage');
            $table->dropColumn('igst_percentage');
            $table->dropColumn('tcs_percentage');
            $table->dropColumn('discount_amount');
            $table->dropColumn('currency');
            $table->dropColumn('description');
        });
    }
};
