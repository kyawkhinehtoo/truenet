<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDnPortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dn_ports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('port')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('input_dbm')->nullable();
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
        Schema::dropIfExists('dn_ports');
    }
}
