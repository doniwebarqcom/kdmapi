<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSpecialOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_special_offers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kodami_product_id')->length(5)->unsigned()->nullable();
            $table->integer('save_money')->default(0)->nullable();
            $table->string('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->string('image')->nullable();
            $table->dateTime('expired_time')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('kodami_product_id')->references('id')->on('kodami_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_special_offers', function (Blueprint $table) {
            $table->dropIfExists('product_special_offers');
            $table->dropForeign('product_special_offers_kodami_product_id_foreign');
        });
    }
}
