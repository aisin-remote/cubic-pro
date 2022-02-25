<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCrToExpenseRequestBudgets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_request_budgets', function (Blueprint $table) {
            $table->string('cr')->after('budget_before')->nullable();
            $table->string('budgt_aft_cr')->after('cr')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_request_budgets', function (Blueprint $table) {
            $table->dropColumn('cr');
            $table->dropColumn('budgt_aft_cr');
        });
    }
}
