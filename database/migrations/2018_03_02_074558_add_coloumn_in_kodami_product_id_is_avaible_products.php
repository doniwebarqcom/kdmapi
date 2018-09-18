<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnInKodamiProductIdIsAvaibleProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('kodami_product_id')->length(5)->unsigned()->nullable()->after('id');
            $table->boolean('is_validate')->default(0)->after('discont_anggota');

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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_validate');
            $table->dropForeign('products_kodami_product_id_foreign');
        });
    }
}
