<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_criteria', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->length(5)->unsigned();
            $table->integer('value_category_criteria_id')->length(5)->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('value_category_criteria_id')->references('id')->on('value_category_criteria');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_criteria', function (Blueprint $table) {
            $table->dropIfExists('product_criteria');
            $table->dropForeign('product_criteria_product_id_foreign');
            $table->dropForeign('product_criteria_value_category_criteria_id_foreign');
        });
    }
}
