<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sap_numbers');
        Schema::create('sap_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number_type');      // e.g: TX, AA, PO (jika PO perlu)
            $table->string('number_booked');    // semua nomor yang terbooking
            $table->string('number_current');   // harus diupdate saat booking number baru, isinya dipotong hanya angkanya saja
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
        Schema::dropIfExists('sap_numbers');
    }
}
