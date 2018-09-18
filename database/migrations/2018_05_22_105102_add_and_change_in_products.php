<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAndChangeInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('koprasi_id')->length(5)->unsigned()->nullable()->change();
            $table->integer('vendor_id')->default(0)->nullable()->after('koprasi_id');
            $table->boolean('ever_validate')->default(0)->nullable()->after('is_validate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
