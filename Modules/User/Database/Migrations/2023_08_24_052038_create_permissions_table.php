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
        Schema::create('permissions', function (Blueprint $table) {
            $table->char('id', 36)->unique();
            $table->bigInteger('seq', true)->index();
            $table->string('name');
            $table->string('slug');
            $table->char('module_id', 36)->index('permissions_module_id_foreign');
            $table->timestamps();
            $table->softDeletes();
            $table->char('created_by', 36)->nullable()->index('permissions_created_by_foreign');
            $table->char('updated_by', 36)->nullable()->index('permissions_updated_by_foreign');
            $table->char('deleted_by', 36)->nullable()->index('permissions_deleted_by_foreign');


            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['updated_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['deleted_by'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['module_id'])->references(['id'])->on('modules')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};
