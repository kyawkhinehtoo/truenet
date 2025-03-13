<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_id');
            $table->longText('action');
            $table->dateTime('datetime');
            $table->unsignedBigInteger('actor_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_histories');
    }
}
