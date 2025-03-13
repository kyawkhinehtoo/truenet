<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_billings', function (Blueprint $table) {
            $table->id();
            $table->string('period_covered');
            $table->string('bill_number');
            $table->unsignedBigInteger('customer_id');
            $table->string('ftth_id');
            $table->string('date_issued');
            $table->text('bill_to')->nullable();
            $table->string('attn')->nullable();
            $table->string('previous_balance')->nullable();
            $table->string('current_charge')->nullable();
            $table->string('compensation')->nullable();
            $table->string('otc')->nullable();
            $table->string('sub_total')->nullable();
            $table->string('payment_duedate')->nullable();
            $table->string('service_description')->nullable();
            $table->string('qty')->nullable();
            $table->string('usage_days')->nullable();
            $table->string('customer_status')->nullable();
            $table->string('normal_cost')->nullable();
            $table->string('type')->nullable();
            $table->string('discount')->nullable();
            $table->integer('total_payable')->nullable();
            $table->string('amount_in_word')->nullable();
            $table->string('commercial_tax')->nullable();
            $table->integer('tax')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('file')->nullable();
            $table->string('bill_month')->nullable();
            $table->string('bill_year')->nullable();
            $table->timestamps();
            $table->integer('popsite_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_billings');
    }
}
