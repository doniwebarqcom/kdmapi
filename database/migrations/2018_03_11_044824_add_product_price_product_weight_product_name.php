<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductPriceProductWeightProductName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->double('product_weight', 3, 2)->default(0)->after('product_id');
            $table->string('product_name')->nullable()->after('product_id');
            $table->integer('product_price')->length(15)->default(0)->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('product_weight');
            $table->dropColumn('product_name');
            $table->dropColumn('product_price');
        });
    }
}
