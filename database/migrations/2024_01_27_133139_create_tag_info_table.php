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
        Schema::create('tag_info', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id');
            $table->integer('en_seo_id');
            $table->text('name');
            $table->text('en_name');
            $table->text('description')->nullable();
            $table->text('en_description')->nullable();
            $table->text('icon')->nullable();
            $table->boolean('flag_show')->default(1);
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
        // Schema::dropIfExists('tag_info');
    }
};