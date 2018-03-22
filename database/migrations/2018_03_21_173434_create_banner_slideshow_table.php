<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerSlideshowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_slideshow', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kodami_product_id')->length(5)->unsigned();
            $table->text('descripsi')->nullable();
            $table->string('image')->nullable();
            $table->integer('urut')->length(2);
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
        Schema::table('banner_slideshow', function (Blueprint $table) {
            $table->dropIfExists('banner_slideshow');
            $table->dropForeign('banner_slideshow_kodami_product_id_foreign');
        });
    }
}
