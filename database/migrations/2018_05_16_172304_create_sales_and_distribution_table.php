<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesAndDistributionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_and_distributions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kodami_product_id')->length(10)->default(0)->nullable();
            $table->integer('vendor_id')->length(10)->default(0)->nullable();
            $table->integer('price')->length(10)->default(0)->nullable();
            $table->date('valid_date')->nullable();
            $table->integer('min_order')->default(0)->nullable();
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
        Schema::dropIfExists('sales_and_distributions');
    }
}
