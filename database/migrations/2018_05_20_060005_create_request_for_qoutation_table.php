<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestForQoutationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_for_qoutation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('case_id')->nullable();
            $table->integer('purchase_type')->nullable();
            $table->string('document_title')->nullable();
            $table->integer('solicatation_type')->nullable();
            $table->integer('currency')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('detail_delivery_address')->nullable();
            $table->date('efective_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_for_qoutation');
    }
}
