<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapUomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sap_uoms');
        Schema::create('sap_uoms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uom_code');
            $table->string('uom_sname');
            $table->string('uom_fname');
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
        Schema::dropIfExists('sap_uoms');
    }
}
