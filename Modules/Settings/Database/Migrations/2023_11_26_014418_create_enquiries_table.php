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
        Schema::create('enquiries', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();

            $table->string('type');
            $table->char('agent_id', 36);
            $table->string('name');
            $table->string('email');
            $table->string('mobile');
            $table->string('salute');
            $table->char('destination_id', 36);
            $table->char('sub_destination_id', 36);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('adult_count');
            $table->integer('child_count');
            $table->integer('infant_count');
            $table->char('lead_source_id', 36);
            $table->string('priority');

            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->foreign(['agent_id'])->references(['id'])->on('agents')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['destination_id'])->references(['id'])->on('destinations')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['sub_destination_id'])->references(['id'])->on('sub_destinations')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['lead_source_id'])->references(['id'])->on('lead_sources')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enquiries');
    }
};
