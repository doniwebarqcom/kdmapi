<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transaction_id')->length(5)->unsigned();
            $table->integer('kodami_product_id')->length(5)->unsigned();
            $table->integer('district_id')->length(5)->unsigned();
            $table->string('recipient_name')->length(100)->nullable();
            $table->string('phone_number_recipient')->length(100)->nullable();
            $table->string('postal_code')->length(100)->nullable();            
            $table->string('quantity')->length(5);            
            $table->text('addres')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->foreign('kodami_product_id')->references('id')->on('kodami_products');
            $table->foreign('district_id')->references('id')->on('districts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_items');
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropIfExists('transaction_items');
            $table->dropForeign('transaction_items_transaction_items_foreign');
            $table->dropForeign('transaction_items_kodami_product_id_foreign');
            $table->dropForeign('transaction_items_district_id_foreign');
        });
    }
}
