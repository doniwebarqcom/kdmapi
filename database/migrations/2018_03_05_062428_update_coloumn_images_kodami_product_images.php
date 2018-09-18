<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColoumnImagesKodamiProductImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kodami_product_images', function (Blueprint $table) {
            $table->renameColumn('images', 'image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kodami_product_images', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
