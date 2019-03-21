<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('approval_detail_id');
            $table->date('pr_receive');
            $table->string('po_number');
            $table->date('po_date');
            $table->string('quotation')->nullable();
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
        Schema::dropIfExists('upload_purchase_orders');
    }
}
