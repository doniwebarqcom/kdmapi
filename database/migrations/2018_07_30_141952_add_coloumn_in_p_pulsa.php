<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnInPPulsa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_pulsa', function (Blueprint $table) {
            $table->integer('hpp')->default(0)->nullable();
            $table->integer('dropsiper_fee')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('p_pulsa', function (Blueprint $table) {
            //
        });
    }
}
