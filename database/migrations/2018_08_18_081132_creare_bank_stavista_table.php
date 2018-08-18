<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreareBankStavistaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_kodami', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama')->nullable();
            $table->string('image')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('owner')->nullable();
            $table->string('moota_bank_id')->nullable();
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
        //
    }
}
