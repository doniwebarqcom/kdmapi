<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnIsVerifyInKoprasi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('koprasi', function (Blueprint $table) {
            $table->boolean('is_verify')->nullable()->default(0)->after('regency_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('koprasi', function (Blueprint $table) {
            //
        });
    }
}
