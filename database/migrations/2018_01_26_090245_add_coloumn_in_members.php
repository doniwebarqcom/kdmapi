<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumnInMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->integer('koprasi_id')->length(5)->unsigned()->after('address')->nullable();
            $table->tinyInteger('have_shop')->lenght(1)->after('address')->default(0);

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
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('have_shop');
            $table->dropColumn('koprasi_id');
            $table->dropForeign('members_koprasi_id_foreign');
        });
    }
}
