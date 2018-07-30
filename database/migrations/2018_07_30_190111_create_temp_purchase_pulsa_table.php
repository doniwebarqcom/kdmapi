<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempPurchasePulsaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_purchase_pulsa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->length(5)->nullable()->unsigned();
            $table->integer('p_pulsa_id')->length(5)->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('p_pulsa_id')->references('id')->on('p_pulsa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_purchase_pulsa', function (Blueprint $table) {
            $table->dropColumn('temp_purchase_pulsa');
            $table->dropForeign('temp_purchase_pulsa_user_id_foreign');
            $table->dropForeign('temp_purchase_pulsa_p_pulsa_id_foreign');
        });
    }
}
