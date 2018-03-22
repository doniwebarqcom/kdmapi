<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNullableInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_group_id')->nullable()->change();
            $table->string('no_anggota')->nullable()->change();
            $table->string('tempat_lahir')->nullable()->change();
            $table->string('tanggal_lahir')->nullable()->change();
            $table->string('jenis_kelamin')->nullable()->change();
            $table->string('alamat')->nullable()->change();
            $table->string('telepon')->nullable()->change();
            $table->string('foto')->nullable()->change();
            $table->string('foto_ktp')->nullable()->change();
            $table->bigInteger('deposit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
