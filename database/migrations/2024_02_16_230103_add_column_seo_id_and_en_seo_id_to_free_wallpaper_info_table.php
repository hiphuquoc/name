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
        Schema::table('free_wallpaper_info', function (Blueprint $table) {
            $table->integer('seo_id')->nullable();
            $table->integer('en_seo_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('free_wallpaper_info', function (Blueprint $table) {
            //
        });
    }
};
