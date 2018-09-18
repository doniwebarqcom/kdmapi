<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDropshiperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dropshipers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->length(5)->unsigned();
            $table->integer('province_id')->length(5)->unsigned();
            $table->integer('district_id')->length(5)->unsigned();
            $table->integer('village_id')->length(5)->unsigned();
            $table->boolean('has_physical_store')->default(0);
            $table->string('occupation')->length(100);
            $table->string('place_to_sell')->length(100);
            $table->string('postal_code')->length(6);
            $table->text('location');
            $table->boolean('is_active')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('village_id')->references('id')->on('villages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dropshipers', function (Blueprint $table) {
            $table->dropIfExists('dropshipers');
            $table->dropForeign('dropshipers_member_id_foreign');
            $table->dropForeign('dropshipers_province_id_foreign');
            $table->dropForeign('dropshipers_district_id_foreign');
            $table->dropForeign('dropshipers_village_id_foreign');
        });
    }
}
