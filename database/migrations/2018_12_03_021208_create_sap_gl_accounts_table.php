<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapGlAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sap_gl_accounts');
        Schema::create('sap_gl_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gl_gcode');
            $table->string('gl_gname');
            $table->string('gl_acode');
            $table->string('gl_aname');
            $table->string('dep_key')->nullable();
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
        Schema::dropIfExists('sap_gl_accounts');
    }
}
