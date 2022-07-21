<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id('key');

            $table->unsignedBigInteger('id')
                ->unique();
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('responsible_user_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('pipeline_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('closed_at')
                ->nullable();
            $table->unsignedBigInteger('created_at')
                ->nullable();
            $table->unsignedBigInteger('updated_at');
            $table->unsignedBigInteger('closest_task_at')
                ->nullable();
            $table->boolean('is_deleted');

            $table->foreign('responsible_user_id')
                ->references('id')
                ->on('amousers');

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
