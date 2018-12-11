<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('approvals');
        Schema::create('approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');  
            $table->boolean('is_seq')->default(0);    
            $table->boolean('is_must_all')->default(0);
            $table->integer('total_approval')->nullable();
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
        Schema::dropIfExists('approvals');
    }
}
