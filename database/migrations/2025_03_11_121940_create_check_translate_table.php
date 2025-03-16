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
        Schema::create('check_translate', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id');
            $table->string('language', 3);
            $table->text('type');
            /* bản dịch hiện tại */
            $table->text('title');
            $table->text('seo_title');
            $table->text('seo_description');
            /* bản dịch mới */
            $table->text('new_title');
            $table->text('new_seo_title');
            $table->text('new_seo_description');
            $table->boolean('status')->default(0);
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
        // Schema::dropIfExists('check_translate');
    }
};
