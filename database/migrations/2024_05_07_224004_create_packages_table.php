<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('speed');
            $table->integer('radius_package')->nullable();
            $table->integer('contract_period');
            $table->enum('type', ['ftth', 'b2b', 'dia', 'mpls'])->default('ftth');
            $table->integer('sla_id')->default(0);
            $table->boolean('status')->default(0);
            $table->integer('price')->nullable();
            $table->enum('currency', ['baht', 'usd', 'mmk'])->nullable();
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
        Schema::dropIfExists('packages');
    }
}
