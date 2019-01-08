<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('approval_details');
        Schema::create('approval_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fyear');
            $table->integer('approval_master_id')->unsigned();
            $table->string('budget_no');
            $table->string('project_name');
            $table->string('sap_gl_account_id')->nullable();
            $table->string('sap_track_no')->nullable();
            $table->string('sap_uom_id')->nullable();
            $table->string('sap_asset_id')->nullable();
            $table->string('sap_cost_center_id')->nullable();
            $table->string('sap_vendor_id')->nullable();
            $table->string('sap_taxe_id')->nullable();
            $table->string('asset_kind')->nullable();
            $table->string('asset_category')->nullable();
            $table->string('cip_no')->nullable();
            $table->string('settlement_date')->nullable();
            $table->string('settlement_name')->nullable();
            $table->decimal('budget_reserved', 17, 2)->nullable();
            $table->decimal('budget_remaining_log', 17, 2)->nullable();
            $table->decimal('actual_qty', 17, 2)->default(0);
            $table->decimal('actual_price_user', 17, 2)->nullable();
            $table->decimal('actual_price_purchasing', 17, 2)->nullable();
            $table->decimal('price_to_download', 17, 0)->default(0)->nullable();
            $table->string('currency')->default('IDR')->nullable();
            $table->string('actual_gr')->nullable();
            $table->text('remarks')->nullable();
            $table->string('po_number')->nullable();
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
        Schema::dropIfExists('approval_details');
    }
}
