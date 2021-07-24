<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('driver_id')->unsigned()->nullable();
            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('latitude_from')->nullable();
            $table->string('longitude_from')->nullable();
            $table->string('latitude_to')->nullable();
            $table->string('longitude_to')->nullable();
            $table->enum('status' , ['0' , '1' , '2' , '3'])->default('0');
            $table->double('price')->nullable();
            $table->double('delivery_price')->nullable();
            $table->enum('commission_status' , ['0' , '1'])->default('0');
            $table->string('commission_value')->nullable();
            $table->enum('payment_type' , ['0' , '1'])->nullable();
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
        Schema::dropIfExists('orders');
    }
}
