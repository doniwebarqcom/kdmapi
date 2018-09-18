<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColoumEfectiveDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_for_qoutation', function (Blueprint $table) {
            $table->renameColumn('efective_date', 'effective_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_for_qoutation', function (Blueprint $table) {
            //
        });
    }
}
