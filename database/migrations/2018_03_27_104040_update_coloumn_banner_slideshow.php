<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColoumnBannerSlideshow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banner_slideshow', function (Blueprint $table) {
            $table->integer('kodami_product_id')->length(5)->unsigned()->nullable()->change();
            $table->integer('type')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banner_slideshow', function (Blueprint $table) {
            //
        });
    }
}
