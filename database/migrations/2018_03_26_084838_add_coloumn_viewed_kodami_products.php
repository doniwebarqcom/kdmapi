<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnViewedKodamiProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kodami_products', function (Blueprint $table) {
            $table->boolean('status')->default(0)->after('weight');
            $table->integer('viewer')->default(0)->after('weight');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kodami_products', function (Blueprint $table) {
            $table->dropColumn('viewer');
            $table->dropColumn('status');
        });
    }
}
