<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfirmationOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirmation_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->length(5)->unsigned()->nullable();
            $table->integer('transaction_id')->nullable();
            $table->boolean('transaction_type')->nullable();
            $table->string('image')->nullable();            
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
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
            $table->dropColumn('confirmation_orders');
            $table->dropForeign('confirmation_orders_user_id_foreign');
        });
    }
}
