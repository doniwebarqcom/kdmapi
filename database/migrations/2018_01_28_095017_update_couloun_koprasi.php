<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCoulounKoprasi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('koprasi', function (Blueprint $table) {
            $table->text('slogan')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->string('image')->nullable()->change();
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
            $table->dropColumn('slogan');
            $table->dropColumn('description');
            $table->dropForeign('image');
        });
    }
}
