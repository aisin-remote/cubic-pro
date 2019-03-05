<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('items');

        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_category_id');
            $table->string('item_code');
            $table->string('item_description');
            $table->string('item_specification');
            $table->string('item_brand');
            $table->float('item_price');
            $table->integer('uom_id');
            $table->integer('supplier_id');
            $table->string('lead_times');
            $table->text('remarks')->nullable();
            $table->string('feature_image')->nullable();
            $table->string('feature_file')->nullable();
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
        Schema::dropIfExists('items');
    }
}
