<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('approval_number')->unique();
            $table->string('budget_type', 3);
            $table->string('dir', 4);
            $table->string('division', 4);
            $table->string('department', 4);
            $table->decimal('total', 17, 2);
            $table->integer('status');
            $table->integer('is_download')->default(0);
            $table->integer('created_by');
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
        Schema::dropIfExists('approval_masters');
    }
}
