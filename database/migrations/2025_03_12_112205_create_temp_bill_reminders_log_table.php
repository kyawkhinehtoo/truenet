<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::dropIfExists('temp_bill_reminder_log');
        Schema::create('temp_bill_reminder_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('name');
            $table->string('ftth_id');
            $table->string('email')->nullable();
            $table->string('phone_1')->nullable();
            $table->unsignedTinyInteger('status_id');
            $table->date('service_off_date');
            $table->integer('days_remaining');
            $table->string('package_name')->nullable();
            $table->decimal('package_price', 10, 2)->nullable();
            $table->enum('sms_status', ['pending', 'sent', 'failed'])->default('pending');
            $table->integer('send_by')->nullable();
            $table->string('onetimecode')->nullable();
            $table->timestamp('sms_sent_at')->nullable();
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->index('service_off_date');
            $table->index('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_bill_reminder_log');
    }
};
