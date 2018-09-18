<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullableDropshiper extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dropshipers', function (Blueprint $table) {
            //$table->integer('province_id')->nullable()->change();
            //$table->integer('regency_id')->nullable()->change();
            //$table->integer('district_id')->nullable()->change();
            //$table->integer('village_id')->nullable()->change();
            $table->string('occupation', 100)->nullable()->change();
            $table->string('place_to_sell', 100)->nullable()->change();
            $table->string('postal_code', 6)->nullable()->change();
            $table->text('location')->nullable()->change();
            $table->string('image')->nullable()->change();
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
            //
        });
    }
}
