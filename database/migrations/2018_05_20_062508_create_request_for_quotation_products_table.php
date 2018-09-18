<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestForQuotationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_for_quotation_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_for_quotation_id')->nullable();
            $table->integer('kodami_product_id')->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_for_quotation_products');
    }
}
