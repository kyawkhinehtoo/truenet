<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpUsageHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_usage_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ip_id');
            $table->unsignedBigInteger('customer_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            
            $table->foreign('customer_id', 'ip_usage_history_customer_id_foreign')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('ip_id', 'ip_usage_history_ip_id_foreign')->references('id')->on('public_ip_addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ip_usage_history');
    }
}
