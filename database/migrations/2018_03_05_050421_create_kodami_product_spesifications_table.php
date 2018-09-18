<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKodamiProductSpesificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kodami_product_spesifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kodami_product_id')->length(5)->unsigned();
            $table->integer('category_spesification_id')->length(5)->unsigned();
            $table->string('value');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('kodami_product_id')->references('id')->on('kodami_products');
            $table->foreign('category_spesification_id')->references('id')->on('category_spesifications');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kodami_product_spesifications', function (Blueprint $table) {
            $table->dropIfExists('kodami_product_spesifications');
            $table->dropForeign('kodami_product_spesifications_kodami_kodami_product_id_foreign');
            $table->dropForeign('kodami_product_spesifications_kodami_category_spesification_id_foreign');
        });
    }
}
