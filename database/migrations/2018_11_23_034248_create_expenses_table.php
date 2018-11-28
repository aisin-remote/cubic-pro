<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('budget_no', 30)->unique();
            $table->string('sap_cc_code')->nullable();
            $table->string('dir', 4)->nullable();
            $table->string('division', 4)->nullable();
            $table->string('department_id', 4);
            $table->string('description');
            $table->decimal('qty_plan', 17, 2);
            $table->decimal('qty_used', 17, 2);
            $table->decimal('qty_remaining', 17, 2);
            $table->decimal('budget_plan', 17, 2);
            $table->decimal('budget_reserved', 17, 2);
            $table->decimal('budget_used', 17, 2);
            $table->decimal('budget_remaining', 17, 2);
            $table->date('plan_gr');
            $table->boolean('is_closed')->default(false);
            $table->boolean('is_revised')->default(false);
            $table->integer('revised_by')->nullable();
            $table->dateTime('revised_at')->nullable();
            $table->integer('status');
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
        Schema::dropIfExists('expenses');
    }
}
