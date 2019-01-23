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
        Schema::dropIfExists('temporary_bom_semi_datas');
        Schema::create('temporary_bom_semi_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('part_id_head');
            $table->string('part_id');
            $table->string('supplier_id');
            $table->string('source');
            $table->string('qty');
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
        Schema::dropIfExists('temporary_bom_semi_datas');
    }
}
