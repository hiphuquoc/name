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
        Schema::create('product_price', function (Blueprint $table) {
            $table->id();
            $table->integer('product_info_id');
            $table->text('name');
            $table->text('description')->nullable();
            $table->text('en_name');
            $table->text('en_description')->nullable();
            $table->text('price'); /* text vì không chỉ là số nguyên dương */
            $table->text('price_before_promotion')->nullable();
            $table->text('price_origin')->nullable();
            $table->text('sale_off')->nullable();
            $table->integer('instock')->nullable(); /* rỗng mặc định bán không giới hạn */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('product_price');
    }
};
