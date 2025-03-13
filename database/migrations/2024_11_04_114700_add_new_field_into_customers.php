<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldIntoCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('vlan')->nullable();
            $table->string('wlan_ssid')->nullable();
            $table->string('wlan_password')->nullable();
            $table->timestamp('service_off_date')->nullable();
            $table->string('promotion_package_plan')->nullable();
            $table->string('refer_bonus')->nullable();
            $table->timestamp('rollback_to_original_package_plan_date')->nullable();
            $table->string('social_account')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}