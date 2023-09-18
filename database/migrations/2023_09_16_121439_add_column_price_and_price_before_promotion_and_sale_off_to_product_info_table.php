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
        Schema::table('product_info', function (Blueprint $table) {
            $table->text('price'); /* text vì không phải số nguyên */
            $table->text('price_before_promotion')->nullable(); /* text vì không phải số nguyên */
            $table->integer('sale_off')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('product_info', function (Blueprint $table) {
        //     //
        // });
    }
};
