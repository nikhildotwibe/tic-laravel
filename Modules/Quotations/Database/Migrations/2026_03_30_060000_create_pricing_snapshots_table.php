<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pricing_snapshots', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();
            $table->char('itinerary_id', 36);
            $table->json('snapshot_data');
            $table->double('grand_total')->default(0);
            $table->string('currency')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();

            $table->foreign(['itinerary_id'])->references(['id'])->on('itineraries')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pricing_snapshots');
    }
};
