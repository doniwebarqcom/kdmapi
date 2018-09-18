<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJunkCategoryCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('junk_category_criteria', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->length(5)->unsigned();
            $table->integer('category_criteria_id')->length(5)->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('category_criteria_id')->references('id')->on('category_criteria');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('junk_category_criteria', function (Blueprint $table) {
            $table->dropIfExists('junk_category_criteria');
            $table->dropForeign('junk_category_criteria_category_id_foreign');
            $table->dropForeign('junk_category_criteria_category_criteria_id_foreign');
        });
    }
}
