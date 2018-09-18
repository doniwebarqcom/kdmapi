<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWholesalePriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesale_price', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->length(5)->unsigned();
            $table->integer('from')->length(5);
            $table->integer('to')->length(5);
            $table->double('price', 8, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wholesale_price', function (Blueprint $table) {
            $table->dropIfExists('wholesale_price');
            $table->dropForeign('wholesale_price_products_id_foreign');
        });
    }
}
