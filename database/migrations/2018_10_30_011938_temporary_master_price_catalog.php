<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TemporaryMasterPriceCatalog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('temporary_master_price_catalogs');
        Schema::create('temporary_master_price_catalogs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('part_id');
            $table->string('supplier_id');
            $table->string('source');
            $table->string('fiscal_year');
            $table->double('price', 25, 2);
            $table->string('supplier_code')->nullable();
            $table->string('part_number')->nullable();
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
        Schema::dropIfExists('temporary_master_price_catalogs');
    }
}
