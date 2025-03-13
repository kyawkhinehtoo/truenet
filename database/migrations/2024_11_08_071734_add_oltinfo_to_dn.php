<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOltinfoToDn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dn_ports', function (Blueprint $table) {
            $table->unsignedBigInteger('pop_device_id');
            $table->integer('gpon_frame')->nullable();
            $table->integer('gpon_slot')->nullable();
            $table->integer('gpon_port')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dn_ports', function (Blueprint $table) {
            //
        });
    }
}