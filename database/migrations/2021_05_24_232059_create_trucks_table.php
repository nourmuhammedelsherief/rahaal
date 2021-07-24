<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrucksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trucks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('truck_type_id')->unsigned();
            $table->foreign('truck_type_id')
                ->references('id')
                ->on('truck_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->bigInteger('vehicle_brand_id')->unsigned();
            $table->foreign('vehicle_brand_id')
                ->references('id')
                ->on('vehicle_brands')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('model_year')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('maximum_round')->nullable();
            $table->string('id_photo')->nullable();
            $table->string('car_form')->nullable();
            $table->string('driver_license')->nullable();
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
        Schema::dropIfExists('trucks');
    }
}
