<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMemberPlacePickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_place_pickups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->length(5)->unsigned();
            $table->integer('district_id')->length(5)->unsigned();
            $table->string('place_name')->length(100);
            $table->string('recipient_name')->length(100);
            $table->string('phone_number_recipient')->length(100);
            $table->string('postal_code')->length(10);
            $table->text('addres');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('member_id')->references('id')->on('members');
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
        Schema::table('member_place_pickups', function (Blueprint $table) {
            $table->dropIfExists('member_place_pickups');
            $table->dropForeign('member_place_pickups_member_id_foreign');
            $table->dropForeign('member_place_pickups_district_id_foreign');
        });
    }
}
