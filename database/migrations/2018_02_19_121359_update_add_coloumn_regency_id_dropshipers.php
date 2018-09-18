<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddColoumnRegencyIdDropshipers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dropshipers', function (Blueprint $table) {
            $table->integer('regency_id')->length(5)->unsigned()->after('province_id');
            $table->foreign('regency_id')->references('id')->on('regencies');
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
            $table->dropColumn('regency_id');
            $table->dropForeign('dropshipers_regency_id_foreign');
        });
    }
}
