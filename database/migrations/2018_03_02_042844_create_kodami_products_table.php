<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKodamiProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kodami_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->length(3)->unsigned();
            $table->string('name');
            $table->string('name_alias');
            $table->text('description')->nullable();
            $table->text('long_description')->nullable();
            $table->double('price');
            $table->string('primary_image');
            $table->double('discont_anggota', 5, 2)->default(0)->nullable();
            $table->double('discont', 5, 2)->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kodami_products', function (Blueprint $table) {
            $table->dropIfExists('kodami_products');
            $table->dropForeign('kodami_products_category_id_foreign');
        });
    }
}
