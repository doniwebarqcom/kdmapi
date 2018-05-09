<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoryHomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_category_home', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kodami_product_id')->length(5)->unsigned();
            $table->integer('category_home_id')->length(5)->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('kodami_product_id')->references('id')->on('kodami_products');
            $table->foreign('category_home_id')->references('id')->on('category_home');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_category_home', function (Blueprint $table) {
            $table->dropIfExists('product_category_home');
            $table->dropForeign('product_category_home_kodami_product_id_foreign');
            $table->dropForeign('product_category_home_category_home_id_foreign');
        });
    }
}
