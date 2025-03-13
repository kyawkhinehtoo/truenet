<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('ftth_id');
            $table->string('name');
            $table->string('nrc')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->longText('address');
            $table->string('location')->nullable();
            $table->date('order_date')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('prefer_install_date')->nullable();
            $table->string('sale_channel')->nullable();
            $table->longText('sale_remark')->nullable();
            $table->unsignedBigInteger('township_id');
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('sale_person_id')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('subcom_id')->nullable();
            $table->unsignedBigInteger('sn_id')->nullable();
            $table->string('splitter_no')->nullable();
            $table->string('fiber_distance')->nullable();
            $table->longText('installation_remark')->nullable();
            $table->integer('fc_used')->nullable();
            $table->integer('fc_damaged')->nullable();
            $table->string('onu_serial')->nullable();
            $table->string('onu_power')->nullable();
            $table->string('contract_term')->nullable();
            $table->integer('foc')->nullable();
            $table->integer('foc_period')->nullable();
            $table->string('advance_payment')->nullable();
            $table->string('advance_payment_day')->nullable();
            $table->string('extra_bandwidth')->nullable();
            $table->integer('deleted')->nullable();
            $table->string('pppoe_account', 50)->nullable();
            $table->string('pppoe_password', 50)->nullable();
            $table->integer('customer_type')->nullable();
            $table->string('bundle')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('project_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
