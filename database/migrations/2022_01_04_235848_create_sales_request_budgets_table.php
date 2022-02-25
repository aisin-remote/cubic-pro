<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesRequestBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_request_budgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('acc_code')->nullable();
            $table->string('acc_name')->nullable();
            $table->string('group')->nullable();
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
            $table->string('fy_first')->nullable();
            $table->string('fy_second')->nullable();
            $table->string('fy_total')->nullable();
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
        Schema::dropIfExists('sales_request_budgets');
    }
}
