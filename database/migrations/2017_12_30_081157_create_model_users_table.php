<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nik', 100)->unique();
            $table->string('no_anggota');
            $table->string('tempat_lahir');
            $table->string('tanggal_lahir');
            $table->string('jenis_kelamin', 25);
            $table->string('alamat');
            $table->string('telepon');
            $table->string('foto');
            $table->string('foto_ktp');
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('name');
            $table->bigInteger('deposit');
            $table->integer('user_group_id');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
