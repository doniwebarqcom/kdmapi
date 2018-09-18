<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChoiceOfOurProductInFrontsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {    
        Schema::create('choice_of_our_product_front', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kodami_product_id')->length(5)->unsigned();
            $table->text('descripsi')->nullable();
            $table->string('image')->nullable();
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
        Schema::table('choice_of_our_product_front', function (Blueprint $table) {
            $table->dropIfExists('choice_of_our_product_front');
            $table->dropForeign('choice_of_our_product_front_kodami_product_id_foreign');
        });
    }
}
