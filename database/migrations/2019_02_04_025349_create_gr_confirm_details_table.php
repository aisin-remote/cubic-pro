<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrConfirmDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gr_confirm_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gr_confirm_id');
            $table->string('gr_no');
            $table->string('budget_no');
            $table->integer('item_id');
            $table->string('item_name');
            $table->string('uom');
            $table->decimal('qty_order');
            $table->decimal('qty_receive');
            $table->decimal('qty_outstanding');
            $table->text('notes');
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
        Schema::dropIfExists('gr_confirm_details');
    }
}
