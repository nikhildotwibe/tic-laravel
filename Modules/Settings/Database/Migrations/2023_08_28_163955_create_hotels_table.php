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
        Schema::create('hotels', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();

            $table->string('name');
            $table->char('destination_id', 36)->nullable();
            $table->char('sub_destination_id', 36)->nullable();
            $table->string('place');
            $table->char('category_id', 36);
            $table->char('property_type_id', 36)->nullable();
            $table->string('sales_email');
            $table->string('contact_no')->nullable();
            $table->string('reservation_no')->nullable();
            $table->string('reservation_email')->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number');

            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->foreign(['destination_id'])->references(['id'])->on('destinations')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['sub_destination_id'])->references(['id'])->on('sub_destinations')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['category_id'])->references(['id'])->on('categories')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['property_type_id'])->references(['id'])->on('property_types')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotels');
    }
};
