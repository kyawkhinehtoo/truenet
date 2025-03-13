<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicIpAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_ip_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique('public_ip_addresses_ip_address_unique');
            $table->string('description')->nullable();
            $table->decimal('annual_charge', 10, 2);
            $table->string('currency');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->timestamps();
            
            $table->foreign('customer_id', 'public_ip_addresses_customer_id_foreign')->references('id')->on('customers')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_ip_addresses');
    }
}
