<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('parts');
        Schema::create('parts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('part_number');
            $table->string('part_name');
            $table->string('uom')->nullable();
            $table->string('plant')->nullable();
            $table->string('category_part')->nullable();
            $table->string('product_code')->nullable();
            $table->string('category_fg')->nullable();
            $table->string('assy_part')->nullable();
            $table->string('group_material')->nullable();
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
        Schema::dropIfExists('parts');
    }
}
