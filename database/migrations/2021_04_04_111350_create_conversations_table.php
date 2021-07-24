<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('first')->unsigned();
            $table->foreign('first')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('second')->unsigned();
            $table->foreign('second')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->enum('first_online' , ['0' , '1'])->default('0');
            $table->enum('second_online' , ['0' , '1'])->default('0');
            $table->enum('seen' , ['0' , '1'])->default('0');
            $table->enum('block' , ['0' , '1'])->default('0');
            $table->bigInteger('block_maker')->unsigned()->nullable();
            $table->foreign('block_maker')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->text('block_reason')->nullable();
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
        Schema::dropIfExists('conversations');
    }
}
