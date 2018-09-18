<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnWeightKodamiProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kodami_products', function (Blueprint $table) {
            $table->double('weight', 5, 2)->default(1)->after('discont');
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
            $table->dropColumn('weight');
        });
    }
}
