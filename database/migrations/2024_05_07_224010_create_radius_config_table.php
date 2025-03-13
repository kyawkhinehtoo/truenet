<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRadiusConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('radius_config', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('server', 50);
            $table->integer('port')->nullable();
            $table->integer('enabled')->nullable();
            $table->string('admin_user', 50)->nullable();
            $table->string('admin_password', 50)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('radius_config');
    }
}
