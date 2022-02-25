<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEquipmentToCapexRequestBudgets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capex_request_budgets', function (Blueprint $table) {
            $table->string('equipment')->after('items_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('capex_request_budgets', function (Blueprint $table) {
            $table->dropColumn('equipment');
        });
    }
}
