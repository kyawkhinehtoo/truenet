<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('priority', 50)->default('');
            $table->unsignedBigInteger('incharge_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('type', 50)->nullable();
            $table->string('topic', 50)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->bigInteger('package_id')->nullable();
            $table->text('new_address')->nullable();
            $table->bigInteger('new_township')->nullable();
            $table->string('location', 50)->nullable();
            $table->date('relocation')->nullable();
            $table->text('description')->nullable();
            $table->string('status');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->date('close_date')->nullable();
            $table->time('close_time')->nullable();
            $table->bigInteger('closed_by')->nullable();
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
        Schema::dropIfExists('incidents');
    }
}
