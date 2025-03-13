<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('period_covered');
            $table->date('end_date')->nullable()->after('start_date');

            $table->integer('usage_day')->nullable()->after('usage_days');
            $table->integer('usage_month')->nullable()->after('usage_day');

            $table->integer('bonus_day')->nullable()->after('usage_month');
            $table->integer('bonus_month')->nullable()->after('bonus_day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
