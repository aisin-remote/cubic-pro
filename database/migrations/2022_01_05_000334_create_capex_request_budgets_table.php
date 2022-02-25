<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapexRequestBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capex_request_budgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dept')->nullable();
            $table->string('budget_no')->nullable();
            $table->string('line')->nullable();
            $table->string('profit_center')->nullable();
            $table->string('profit_center_code')->nullable();
            $table->string('cost_center')->nullable();
            $table->string('type')->nullable();
            $table->string('project_name')->nullable();
            $table->string('import_domestic')->nullable();
            $table->string('items_name')->nullable();
            $table->string('qty')->nullable();
            $table->string('curency')->nullable();
            $table->string('original_price')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->string('price')->nullable();
            $table->string('sop')->nullable();
            $table->string('first_dopayment_term')->nullable();
            $table->string('first_dopayment_amount')->nullable();
            $table->string('final_payment_term')->nullable();
            $table->string('final_payment_amount')->nullable();
            $table->string('owner_asset')->nullable();
            $table->string('april')->nullable();
            $table->string('mei')->nullable();
            $table->string('juni')->nullable();
            $table->string('juli')->nullable();
            $table->string('agustus')->nullable();
            $table->string('september')->nullable();
            $table->string('oktober')->nullable();
            $table->string('november')->nullable();
            $table->string('december')->nullable();
            $table->string('januari')->nullable();
            $table->string('februari')->nullable();
            $table->string('maret')->nullable();
            $table->string('department_id')->nullable();

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
        Schema::dropIfExists('capex_request_budgets');
    }
}
