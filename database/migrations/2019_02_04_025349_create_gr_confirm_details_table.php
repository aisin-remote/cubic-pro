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
            $table->integer('approval_detail_id');
            $table->string('gr_no')->nullable();
            $table->decimal('qty_order')->default(0);
            $table->decimal('qty_receive')->default(0);
            $table->decimal('qty_outstanding')->default(0);
            $table->text('notes')->nullable();
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
