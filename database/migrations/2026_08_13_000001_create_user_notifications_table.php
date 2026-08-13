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
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('type')->nullable()->default('enquiry_confirmed');
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->unsignedBigInteger('enquiry_id')->nullable();
            $table->unsignedBigInteger('itinerary_id')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->nullable()->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notifications');
    }
};
