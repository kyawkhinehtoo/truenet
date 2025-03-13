<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('receipt_number')->nullable();
            $table->integer('month');
            $table->string('year');
            $table->string('bill_no');
            $table->unsignedBigInteger('bill_id')->nullable();
            $table->unsignedBigInteger('invoice_id');
            $table->string('status', 200)->default('outstanding');
            $table->string('issue_amount')->default('0');
            $table->string('issue_currenty')->default('MMK');
            $table->unsignedBigInteger('receipt_person')->nullable();
            $table->string('payment_channel', 200)->nullable();
            $table->unsignedBigInteger('collected_person')->nullable();
            $table->timestamp('receipt_date')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->text('remark')->nullable();
            $table->string('collected_amount')->default('0');
            $table->string('outstanding_amount')->default('0');
            $table->string('collected_currency')->default('MMK');
            $table->string('collected_exchangerate')->nullable();
            $table->string('receipt_file')->nullable();
            $table->string('receipt_url')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->foreign('customer_id', 'receipt_records_ibfk_1')->references('id')->on('customers');
            $table->foreign('invoice_id', 'receipt_records_ibfk_2')->references('id')->on('invoices');
            $table->foreign('bill_id', 'receipt_records_ibfk_3')->references('id')->on('bills');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_records');
    }
}
