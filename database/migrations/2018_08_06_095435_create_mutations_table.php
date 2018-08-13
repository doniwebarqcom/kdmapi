<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_id')->length(5)->unsigned()->nullable();
            $table->datetime('date_transfer')->nullable();
            $table->string('description')->nullable();
            $table->integer('amount')->nullable();
            $table->boolean('type')->nullable();
            $table->string('note')->nullable();
            $table->string('mutation_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('bank_id')->references('id')->on('bank');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->dropColumn('mutations');
            $table->dropForeign('mutations_bank_id_foreign');
        });
    }
}
