<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pop_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pop_id');
            $table->string('device_name');
            $table->integer('qty')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
            
            $table->foreign('pop_id', 'pop_devices_pop_id_foreign')->references('id')->on('pops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pop_devices');
    }
}
