<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sms_gateway', function (Blueprint $table) {
            $table->string('single_sms')->nullable();
            $table->string('info')->nullable();
            $table->string('bulk_sms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_gateway', function (Blueprint $table) {
            //
        });
    }
};
