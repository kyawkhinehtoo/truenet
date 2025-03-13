<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('announcement_id');
            $table->string('title');
            $table->text('detail');
            $table->string('status')->nullable();
            $table->string('type');
            $table->text('message_id')->nullable();
            $table->timestamp('date')->useCurrent()->useCurrentOnUpdate();
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
        Schema::dropIfExists('announcement_log');
    }
}
