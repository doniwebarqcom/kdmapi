<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_criteria', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->length(5)->unsigned();
            $table->string('label')->length(100);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_criteria', function (Blueprint $table) {
            $table->dropIfExists('category_criteria');
            $table->dropForeign('category_criteria_category_id_foreign');
        });
    }
}
