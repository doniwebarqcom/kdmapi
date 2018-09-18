<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrateAndDeleteUserInVendors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
                $table->dropForeign('vendors_member_id_foreign');
                $table->dropColumn('member_id');

                $table->integer('koprasi_id')->length(5)->after('id')->unsigned()->nullable();
                $table->foreign('koprasi_id')->references('id')->on('koprasi');
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
            $table->dropForeign('vendors_koprasi_id_foreign');
        });
    }
}
