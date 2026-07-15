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
        Schema::create('room_rate_exceptions', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();

            $table->char('room_id', 36);
            $table->date('exception_date');
            $table->string('label', 100)->nullable();

            // Bed amounts — nullable so only enabled types need to be stored
            $table->double('single_bed_amount')->nullable();
            $table->double('double_bed_amount')->nullable();
            $table->double('triple_bed_amount')->nullable();
            $table->double('extra_bed_amount')->nullable();
            $table->double('child_w_bed_amount')->nullable();
            $table->double('child_n_bed_amount')->nullable();
            $table->double('quad_bed_amount')->nullable();
            $table->double('two_bedroom_amount')->nullable();
            $table->double('three_bedroom_amount')->nullable();
            $table->double('four_bedroom_amount')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['room_id'])->references(['id'])->on('rooms')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_rate_exceptions');
    }
};
