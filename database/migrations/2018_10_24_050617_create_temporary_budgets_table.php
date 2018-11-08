<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemporaryBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('temporary_budgets');
        Schema::create('temporary_budgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('part_id');
            $table->string('fiscal_year')->nullable();
            $table->string('part_number')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('customer_id');
            $table->string('market')->nullable();
            $table->integer('jan_qty')->default(0);
            $table->double('jan_amount', 25,2)->default(0);
            $table->integer('feb_qty')->default(0);
            $table->double('feb_amount', 25,2)->default(0);
            $table->integer('mar_qty')->default(0);
            $table->double('mar_amount', 25,2)->default(0);
            $table->integer('apr_qty')->default(0);
            $table->double('apr_amount', 25,2)->default(0);
            $table->integer('may_qty')->default(0);
            $table->double('may_amount', 25,2)->default(0);
            $table->integer('june_qty')->default(0);
            $table->double('june_amount', 25,2)->default(0);
            $table->integer('july_qty')->default(0);
            $table->double('july_amount', 25,2)->default(0);
            $table->integer('august_qty')->default(0);
            $table->double('august_amount', 25,2)->default(0);
            $table->integer('sep_qty')->default(0);
            $table->double('sep_amount', 25,2)->default(0);
            $table->integer('okt_qty')->default(0);
            $table->double('okt_amount', 25,2)->default(0);
            $table->integer('nov_qty')->default(0);
            $table->double('nov_amount', 25,2)->default(0);
            $table->string('des_qty')->default(0);
            $table->string('des_amount', 25,2)->default(0);
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
        Schema::dropIfExists('temporary_budgets');
    }
}
