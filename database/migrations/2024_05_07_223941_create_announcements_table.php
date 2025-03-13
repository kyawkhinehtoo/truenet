<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('general')->nullable();
            $table->string('packages')->nullable();
            $table->string('packages_invert')->nullable();
            $table->string('townships')->nullable();
            $table->string('townships_invert')->nullable();
            $table->string('projects')->nullable();
            $table->string('projects_invert')->nullable();
            $table->string('customer_status')->nullable();
            $table->string('customer_status_invert')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('deposit_status')->nullable();
            $table->string('announcement_type');
            $table->unsignedBigInteger('template_id');
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
        Schema::dropIfExists('announcements');
    }
}
