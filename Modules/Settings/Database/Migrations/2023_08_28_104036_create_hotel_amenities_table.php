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
        Schema::create('hotel_amenities', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();

            $table->char('amenity');

            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_amenities');
    }
};
