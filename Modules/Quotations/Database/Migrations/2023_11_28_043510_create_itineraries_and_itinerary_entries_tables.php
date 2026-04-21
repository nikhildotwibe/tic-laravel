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
        Schema::create('itineraries', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();

            $table->string('package_name');
            $table->char('enquiry_id', 36);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('adult_count');
            $table->integer('child_count');
            $table->char('destination_id', 36);
            $table->date('valid_until');
            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->foreign(['enquiry_id'])->references(['id'])->on('enquiries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['destination_id'])->references(['id'])->on('destinations')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });


        Schema::create('itinerary_entries', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();

            $table->date('date');
            $table->char('itinerary_id', 36);
            $table->string('entry_type'); // hotel , activity , transfer
            $table->integer('no_of_person');

            // hotel specific
            $table->integer('single_count')->default(0);
            $table->integer('double_count')->default(0);
            $table->integer('triple_count')->default(0);
            $table->integer('extra_count')->default(0);
            $table->integer('child_w_count')->default(0);
            $table->integer('child_n_count')->default(0);

            // activity specific
            $table->text('description')->nullable();

            // transfer specific
            $table->string('transfer_type')->nullable(); // private , SIC
            $table->double('cost')->default(0);
            $table->double('adult_cost')->default(0);
            $table->double('child_cost')->default(0);


            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date');
            $table->time('end_time');

            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->foreign(['itinerary_id'])->references(['id'])->on('itineraries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itinerary_entries');
        Schema::dropIfExists('itineraries');
    }
};
