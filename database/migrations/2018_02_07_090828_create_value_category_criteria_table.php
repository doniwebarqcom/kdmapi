<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValueCategoryCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('value_category_criteria', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_criteria_id')->length(5)->unsigned();
            $table->string('value')->length(255);            
            $table->timestamps();
            $table->softDeletes();
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
        Schema::table('value_category_criteria', function (Blueprint $table) {
            $table->dropIfExists('value_category_criteria');
            $table->dropForeign('value_category_criteria_category_criteria_id_foreign');
        });
    }
}
