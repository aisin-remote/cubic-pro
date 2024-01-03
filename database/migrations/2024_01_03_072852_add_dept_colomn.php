<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeptColomn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('direct_material_request_budgets', function (Blueprint $table) {
            $table->string('dept')->nullable()->after('id'); // Adds the "dept" column after the "id" column in table direct_material_request_budgets
        });

        Schema::table('sales_request_budgets', function (Blueprint $table) {
            $table->string('dept')->nullable()->after('id'); // Adds the "dept" column after the "id" column in table sales_request_budgets
        });

        Schema::table('labor_request_budgets', function (Blueprint $table) {
            $table->string('dept')->nullable()->after('id'); // Adds the "dept" column after the "id" column in table labor_request_budgets
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
