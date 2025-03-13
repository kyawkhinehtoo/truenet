<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptSummeryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_summery', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->unsignedBigInteger('1')->nullable();
            $table->unsignedBigInteger('2')->nullable();
            $table->unsignedBigInteger('3')->nullable();
            $table->unsignedBigInteger('4')->nullable();
            $table->unsignedBigInteger('5')->nullable();
            $table->unsignedBigInteger('6')->nullable();
            $table->unsignedBigInteger('7')->nullable();
            $table->unsignedBigInteger('8')->nullable();
            $table->unsignedBigInteger('9')->nullable();
            $table->unsignedBigInteger('10')->nullable();
            $table->unsignedBigInteger('11')->nullable();
            $table->unsignedBigInteger('12')->nullable();
            $table->string('year');
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
        Schema::dropIfExists('receipt_summery');
    }
}
