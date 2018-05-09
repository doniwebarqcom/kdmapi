<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddsHomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adds_homes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kodami_product_id')->length(5)->unsigned()->nullable();
            $table->string('image')->nullable();
            $table->string('width')->length(5)->default(100);
            $table->string('height')->length(5)->default(100);
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
        Schema::table('adds_homes', function (Blueprint $table) {
            $table->dropIfExists('adds_homes');
            $table->dropForeign('adds_homes_kodami_product_id_foreign');
        });
    }
}
