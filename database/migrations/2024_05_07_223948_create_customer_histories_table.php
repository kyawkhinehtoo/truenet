<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('actor_id');
            $table->string('type', 50)->default('');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->bigInteger('old_package')->default(0);
            $table->bigInteger('new_package')->default(0);
            $table->bigInteger('old_status')->default(0);
            $table->bigInteger('new_status')->default(0);
            $table->text('old_address')->nullable();
            $table->string('old_location')->nullable();
            $table->text('new_address')->nullable();
            $table->string('new_location')->nullable();
            $table->integer('active');
            $table->timestamp('date')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_histories');
    }
}
