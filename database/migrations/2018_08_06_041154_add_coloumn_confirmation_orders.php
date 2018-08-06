<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnConfirmationOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('confirmation_orders', function (Blueprint $table) {
            $table->integer('bank_id')->length(5)->unsigned()->nullable();
            $table->integer('total_transfer')->nullable();
            $table->boolean('status')->default(0);

            $table->foreign('bank_id')->references('id')->on('bank');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('confirmation_orders', function (Blueprint $table) {
            $table->dropForeign('confirmation_orders_bank_id_foreign');
        });
    }
}
