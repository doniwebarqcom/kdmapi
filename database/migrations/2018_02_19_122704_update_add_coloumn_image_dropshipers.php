<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColoumnImageDropshipers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dropshipers', function (Blueprint $table) {
            $table->string('image')->after('location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dropshipers', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
