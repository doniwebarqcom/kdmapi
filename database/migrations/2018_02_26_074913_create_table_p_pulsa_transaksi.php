<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePPulsaTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_pulsa_transaksi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_invoice');
            $table->integer('user_id');
            $table->integer('pulsa_id');
            $table->integer('nominal');
            $table->string('no_telepon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_pulsa_transaksi');
    }
}
