<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKodamiProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kodami_product_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kodami_product_id')->length(5)->unsigned();
            $table->string('images');
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
        Schema::table('kodami_product_images', function (Blueprint $table) {
            $table->dropIfExists('kodami_product_images');
            $table->dropForeign('kodami_product_images_kodami_product_id_foreign');
        });
    }
}
