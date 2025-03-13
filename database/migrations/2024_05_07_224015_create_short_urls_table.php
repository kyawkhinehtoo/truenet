<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id();
            $table->text('destination_url');
            $table->string('url_key')->unique('short_urls_url_key_unique');
            $table->string('default_short_url');
            $table->tinyInteger('single_use');
            $table->tinyInteger('track_visits');
            $table->integer('redirect_status_code')->default(301);
            $table->boolean('track_ip_address')->default(0);
            $table->boolean('track_operating_system')->default(0);
            $table->boolean('track_operating_system_version')->default(0);
            $table->boolean('track_browser')->default(0);
            $table->boolean('track_browser_version')->default(0);
            $table->boolean('track_referer_url')->default(0);
            $table->boolean('track_device_type')->default(0);
            $table->timestamp('activated_at')->nullable()->default('2023-07-09 14:35:17');
            $table->timestamp('deactivated_at')->nullable();
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
        Schema::dropIfExists('short_urls');
    }
}
