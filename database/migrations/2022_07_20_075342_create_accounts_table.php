<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id('local_id');

            $table->unsignedBigInteger('id')
                ->unique();
            $table->string('name');
            $table->string('subdomain');
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->integer('created_by')
                ->default(0);
            $table->integer('updated_by')
                ->default(0);
            $table->unsignedBigInteger('current_user_id');
            $table->string('country');
            $table->string('customers_mode');
            $table->boolean('is_unsorted_on');
            $table->boolean('is_loss_reason_enabled');
            $table->boolean('is_helpbot_enabled');
            $table->boolean('is_technical_account');
            $table->string('amojo_id')
                ->nullable();
            $table->integer('version')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
