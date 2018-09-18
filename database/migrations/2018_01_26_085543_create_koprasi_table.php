<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKoprasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('koprasi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('regency_id')->length(5)->unsigned();
            $table->string('name')->unique();
            $table->string('url')->unique();
            $table->text('slogan');
            $table->text('description');
            $table->string('image');            
            $table->text('pickup_address');
            $table->string('postal_code')->length(8);
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('koprasi');
        Schema::table('koprasi', function (Blueprint $table) {
            Schema::dropForeign('koprasi_regency_id_foreign');
        });
    }
}
