<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalDtlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('approval_dtls');
        Schema::create('approval_dtls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('approval_id');
            $table->integer('user_id');
            $table->integer('level');
            $table->timestamps();
            $table->unique(['approval_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_dtls');
    }
}
