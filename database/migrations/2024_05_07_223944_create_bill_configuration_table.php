<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_configuration', function (Blueprint $table) {
            $table->id();
            $table->string('exclude_list')->nullable();
            $table->integer('mrc_day')->nullable();
            $table->integer('prepaid_day')->nullable();
            $table->integer('mrc_month')->nullable();
            $table->integer('prepaid_month')->nullable();
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
        Schema::dropIfExists('bill_configuration');
    }
}
