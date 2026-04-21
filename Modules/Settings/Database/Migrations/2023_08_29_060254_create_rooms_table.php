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
        Schema::create('rooms', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();

            $table->char('hotel_id', 36);
            $table->char('market_type_id', 36)->nullable();
            $table->date('from_date');
            $table->date('to_date');
            $table->char('room_type_id', 36)->nullable();
            $table->double('single_bed_amount')->default(0);
            $table->double('double_bed_amount')->default(0);
            $table->boolean('is_triple_bed_available')->default(false);
            $table->double('triple_bed_amount')->default(0);
            $table->boolean('is_extra_bed_available')->default(false);
            $table->double('extra_bed_amount')->default(0);
            $table->boolean('is_child_w_bed_available')->default(false);
            $table->double('child_w_bed_amount')->default(0);
            $table->boolean('is_child_n_bed_available')->default(false);
            $table->double('child_n_bed_amount')->default(0);
            $table->integer('occupancy');

            $table->boolean('is_allotted')->default(false);
            $table->integer('allotted_cut_off_days')->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->foreign(['market_type_id'])->references(['id'])->on('market_types')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['room_type_id'])->references(['id'])->on('room_types')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['hotel_id'])->references(['id'])->on('hotels')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });



        Schema::create('room_meal_plan_entries', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();

            $table->char('room_id', 36);
            $table->char('meal_plan_id', 36);
            $table->double('amount')->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->foreign(['room_id'])->references(['id'])->on('rooms')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['meal_plan_id'])->references(['id'])->on('meal_plans')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });


        Schema::create('room_amenity_entries', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();

            $table->char('room_id', 36);
            $table->char('room_amenity_id', 36);

            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->foreign(['room_id'])->references(['id'])->on('rooms')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['room_amenity_id'])->references(['id'])->on('room_amenities')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_amenity_entries');
        Schema::dropIfExists('room_meal_plan_entries');
        Schema::dropIfExists('rooms');
    }
};
