<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallpaper_info', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('name');
            $table->longText('description')->nullable();
            $table->text('file_name');
            $table->text('file_url_hosting');
            $table->text('file_url_cloud');
            $table->integer('width');
            $table->integer('height');
            $table->integer('file_size');
            $table->text('extension');
            $table->text('mime_type');
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
        // Schema::dropIfExists('wallpaper_info');
    }
};
