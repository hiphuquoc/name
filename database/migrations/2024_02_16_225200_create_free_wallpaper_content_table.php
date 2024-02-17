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
        Schema::create('free_wallpaper_content', function (Blueprint $table) {
            $table->id();
            $table->integer('free_wallpaper_info_id');
            $table->text('name');
            $table->longText('content');
            $table->text('en_name');
            $table->longText('en_content');
            $table->integer('ordering')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('free_wallpaper_content');
    }
};
