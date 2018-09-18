<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnPPulsaTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_pulsa_transaksi', function (Blueprint $table) {
            $table->integer('random_code')->default(0);
            $table->integer('dropshiper_user_id')->nullable();
            $table->integer('payment_method')->default(1);
            $table->boolean('dropshiper_status')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('p_pulsa_transaksi', function (Blueprint $table) {
            //
        });
    }
}
