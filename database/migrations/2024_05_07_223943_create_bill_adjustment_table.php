<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillAdjustmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_adjustment', function (Blueprint $table) {
            $table->id();
            $table->string('period_covered');
            $table->string('bill_number');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('bill_id');
            $table->unsignedBigInteger('invoice_number')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->string('ftth_id');
            $table->string('date_issued');
            $table->text('bill_to')->nullable();
            $table->string('attn')->nullable();
            $table->string('previous_balance')->nullable();
            $table->string('current_charge')->nullable();
            $table->string('compensation')->nullable();
            $table->string('otc');
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
            $table->integer('tax_to')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mail_sent_date')->nullable();
            $table->string('sms_sent_date')->nullable();
            $table->string('mail_sent_status')->nullable();
            $table->string('sms_sent_status')->nullable();
            $table->string('reminder_sms_sent_date')->nullable();
            $table->string('reminder_sms_sent_status')->nullable();
            $table->string('file')->nullable();
            $table->string('url')->nullable();
            $table->string('bill_month')->nullable();
            $table->string('bill_year')->nullable();
            $table->string('coupon_id')->nullable();
            $table->string('adjustment_type')->nullable();
            $table->string('adjustment_amount')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('adjusted_by');
            
            $table->foreign('adjusted_by', 'bill_adjustment_adjusted_by_foreign')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_adjustment');
    }
}
