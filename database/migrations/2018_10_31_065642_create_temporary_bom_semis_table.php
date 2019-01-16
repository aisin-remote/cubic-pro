<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemporaryBomSemisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('temporary_bom_semis');
        Schema::create('temporary_bom_semis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('part_id');
            $table->string('supplier_id');
            $table->string('model');
            // $table->string('fiscal_year');
            $table->string('supplier_code')->nullable();
            $table->string('part_number')->nullable();
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
        Schema::dropIfExists('temporary_bom_semis');
    }
}
