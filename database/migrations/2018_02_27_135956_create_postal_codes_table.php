
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostalCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('village_id')->length(5)->unsigned();
            $table->string('code')->length(10);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('village_id')->references('id')->on('villages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postal_codes', function (Blueprint $table) {
            $table->dropIfExists('postal_codes');
            $table->dropForeign('postal_codes_village_id_foreign');
        });
    }
}
