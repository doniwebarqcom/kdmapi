<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSpesificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_spesifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_spesification_id')->length(5)->unsigned();
            $table->string('value');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::table('product_spesifications', function (Blueprint $table) {
            $table->dropIfExists('product_spesifications');
            $table->dropForeign('product_spesifications_category_spesification_id_foreign');
        });
    }
}
