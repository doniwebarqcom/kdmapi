<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnMemberPlacePickupIdAndShippingCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->integer('member_place_pickup_id')->length(5)->unsigned()->after('member_id');
            $table->integer('shipping_cost')->after('quantity');

            $table->foreign('member_place_pickup_id')->references('id')->on('member_place_pickups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('member_place_pickup_id');
            $table->dropColumn('shipping_cost');
            $table->dropForeign('cart_items_member_place_pickup_id_foreign');
        });
    }
}
