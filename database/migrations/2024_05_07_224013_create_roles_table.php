<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('permission')->nullable();
            $table->integer('delete_customer')->nullable();
            $table->integer('read_customer')->nullable();
            $table->integer('read_incident')->nullable();
            $table->integer('write_incident')->nullable();
            $table->integer('edit_invoice')->nullable();
            $table->integer('bill_generation')->nullable();
            $table->integer('bill_receipt')->nullable();
            $table->integer('radius_read')->nullable();
            $table->integer('radius_write')->nullable();
            $table->integer('incident_report')->nullable();
            $table->integer('bill_report')->nullable();
            $table->integer('radius_report')->nullable();
            $table->integer('incident_only')->nullable();
            $table->timestamps();
            $table->integer('read_only_ip')->nullable();
            $table->integer('add_ip')->nullable();
            $table->integer('edit_ip')->nullable();
            $table->integer('delete_ip')->nullable();
            $table->integer('ip_report')->nullable();
            $table->integer('delete_invoice')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
