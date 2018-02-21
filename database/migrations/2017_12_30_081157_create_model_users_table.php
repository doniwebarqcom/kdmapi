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
            $table->string('agama', 25);
            $table->integer('province_id');
            $table->integer('district_id');
            $table->dateTime('last_logged_in_at');
            $table->dateTime('last_logged_out_at');
            $table->integer('access_id');
            $table->rememberToken();
            $table->timestamps();

            $table->softDeletes();
            $table->foregein('province_id')->references('id')->on('provinces');
            $table->foregein('district_id')->references('id')->on('district');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table){
            $table->dropIfExists('users');
            $table->dropForegein('users_province_id_foreign');
            $table->dropForegein('users_district_id_foreign');
        });
    }
}
