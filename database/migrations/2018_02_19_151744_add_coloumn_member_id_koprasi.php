<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnMemberIdKoprasi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('koprasi', function (Blueprint $table) {
            $table->integer('member_id')->length(5)->unsigned()->after('id');
            $table->foreign('member_id')->references('id')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('koprasi', function (Blueprint $table) {
            $table->dropColumn('member_id');
            $table->dropForeign('koprasi_member_id_foreign');
        });
    }
}