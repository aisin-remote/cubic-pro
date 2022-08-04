<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeToRequestBudgetTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('capex_material_request_budgets', function (Blueprint $table) {
        //     $table->string('code')->after('group')->nullable();
        // });

        Schema::table('direct_material_request_budgets', function (Blueprint $table) {
            $table->string('code')->after('group')->nullable();
        });

        Schema::table('expense_request_budgets', function (Blueprint $table) {
            $table->string('code')->after('group')->nullable();
        });

        Schema::table('sales_request_budgets', function (Blueprint $table) {
            $table->string('code')->after('group')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
