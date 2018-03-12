<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCartItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['member_place_pickup_id']);
            $table->dropColumn('member_place_pickup_id');
            $table->integer('district_id')->length(5)->unsigned()->after('member_id');
            $table->text('addres')->nullable()->after('quantity');
            $table->string('postal_code')->length(100)->nullable()->after('quantity');
            $table->string('phone_number_recipient')->length(100)->nullable()->after('quantity');
            $table->string('recipient_name')->length(100)->nullable()->after('quantity');


            $table->foreign('district_id')->references('id')->on('districts');
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
            $table->dropColumn('postal_code');
            $table->dropColumn('phone_number_recipient');
            $table->dropColumn('district_id');
            $table->dropForeign('cart_items_district_id_foreign');
        });
    }
}
