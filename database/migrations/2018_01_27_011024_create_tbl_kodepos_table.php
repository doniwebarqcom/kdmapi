<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblKodeposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kodepos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kelurahan')->length(100);
            $table->string('kecamatan')->length(100);
            $table->string('kabupaten')->length(100);
            $table->string('provinsi')->length(100);
            $table->string('kodepos')->length(5);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_kodepos');
    }
}
