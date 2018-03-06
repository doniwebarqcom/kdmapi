<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->length(5)->unsigned();
            $table->integer('product_id')->length(5)->unsigned();
            $table->string('quantity')->length(5);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('product_id')->references('id')->on('kodami_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIfExists('cart_items');
            $table->dropForeign('cart_items_member_id_foreign');
            $table->dropForeign('cart_items_product_id_foreign');
        });
    }
}
