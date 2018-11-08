<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemporaryBomSemiDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporary_bom_semi_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('temporary_bom_semi_id');
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
        Schema::dropIfExists('temporary_bom_semi_datas');
    }
}
