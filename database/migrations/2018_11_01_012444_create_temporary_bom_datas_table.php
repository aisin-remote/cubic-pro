<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemporaryBomDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('temporary_bom_datas');
        Schema::create('temporary_bom_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('temporary_bom_id')->nullable();
            $table->string('part_id');
            $table->string('supplier_id');
            $table->string('source');
            $table->string('qty');
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
        Schema::dropIfExists('temporary_bom_datas');
    }
}
