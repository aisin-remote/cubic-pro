<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapCostCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sap_cost_centers');
        Schema::create('sap_cost_centers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cc_code');
            $table->string('cc_sname');
            $table->string('cc_fname');
            $table->string('cc_gcode');
            $table->string('cc_gtext');
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
        Schema::dropIfExists('sap_cost_centers');
    }
}
