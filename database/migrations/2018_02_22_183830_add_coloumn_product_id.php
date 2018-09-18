<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnProductId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_spesifications', function (Blueprint $table) {
            $table->integer('product_id')->length(5)->unsigned()->after('id');
            $table->foreign('product_id')->references('id')->on('products');
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
            $table->dropColumn('product_id');
            $table->dropForeign('product_spesifications_product_id_foreign');
        });
    }
}
