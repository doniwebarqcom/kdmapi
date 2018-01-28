<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('koprasi_id')->length(5)->unsigned();
            $table->integer('category_id')->length(3)->unsigned();
            $table->text('description');
            $table->double('price');
            $table->string('primary_image');
            $table->boolean('avaible')->default(false);
            $table->integer('success_transaction')->length(5);
            $table->integer('total_comment')->length(4);
            $table->integer('weight')->length(4);
            $table->integer('viewer')->length(4);
            $table->integer('stock')->length(4);
            $table->boolean('new')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('koprasi_id')->references('id')->on('koprasi');
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
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropIfExists('products');
            $table->dropForeign('products_koprasi_id_foreign');
            $table->dropForeign('products_category_id_foreign');
        });
    }
}
