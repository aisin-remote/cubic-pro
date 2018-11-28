<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sap_assets');
        Schema::create('sap_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('asset_class');
            $table->string('asset_type');
            $table->string('asset_code');
            $table->string('asset_name');
            $table->string('asset_content');
            $table->string('asset_account');
            $table->string('asset_acctext');
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
        Schema::dropIfExists('sap_assets');
    }
}
