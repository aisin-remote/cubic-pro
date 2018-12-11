<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TemporarySalesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('temporary_sales_datas');
        Schema::create('temporary_sales_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('part_id');
            $table->string('fiscal_year')->nullable();
            $table->string('part_number')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('customer_id');
            $table->string('market', 25,2)->nullable();
            $table->double('jan_qty')->default(0);
            $table->double('jan_amount', 25,2)->default(0);
            $table->double('feb_qty', 25,2)->default(0);
            $table->double('feb_amount', 25,2)->default(0);
            $table->double('mar_qty', 25,2)->default(0);
            $table->double('mar_amount', 25,2)->default(0);
            $table->double('apr_qty', 25,2)->default(0);
            $table->double('apr_amount', 25,2)->default(0);
            $table->double('may_qty')->default(0);
            $table->double('may_amount', 25,2)->default(0);
            $table->double('june_qty', 25,2)->default(0);
            $table->double('june_amount', 25,2)->default(0);
            $table->double('july_qty', 25,2)->default(0);
            $table->double('july_amount', 25,2)->default(0);
            $table->double('august_qty', 25,2)->default(0);
            $table->double('august_amount', 25,2)->default(0);
            $table->double('sep_qty', 25,2)->default(0);
            $table->double('sep_amount', 25,2)->default(0);
            $table->double('okt_qty', 25,2)->default(0);
            $table->double('okt_amount', 25,2)->default(0);
            $table->double('nov_qty', 25,2)->default(0);
            $table->double('nov_amount', 25,2)->default(0);
            $table->string('des_qty', 25,2)->default(0);
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
        Schema::dropIfExists('temporary_sales_datas');
        
    }
}
