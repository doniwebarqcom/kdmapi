<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteAndAddColoumnInVendors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            
            $table->dropColumn('currency');
            $table->string('password')->after('name')->nullable();
            $table->string('username')->after('name')->nullable();
            $table->string('detail_address')->after('email')->nullable();
            $table->integer('regency_id')->length(5)->after('email')->unsigned()->nullable();
            $table->integer('member_id')->length(5)->after('id')->unsigned()->nullable();

            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('regency_id')->references('id')->on('regencies');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign('vendors_member_id_foreign');
            $table->dropForeign('vendors_regency_id_foreign');
        });
    }
}
