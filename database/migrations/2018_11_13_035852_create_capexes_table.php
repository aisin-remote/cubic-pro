<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('capexes');
        Schema::create('capexes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('budget_no', 30)->unique();
            $table->string('dir', 4)->nullable();
            $table->string('division_id')->nullable();
            $table->string('department_id');
            $table->string('equipment_name');
            $table->decimal('budget_plan', 17, 2)->default(0);
            $table->decimal('budget_reserved', 17, 2)->default(0);
            $table->decimal('budget_used', 17, 2)->default(0);
            $table->decimal('budget_remaining', 17, 2)->default(0);
            $table->date('plan_gr');
            $table->boolean('is_closed')->default(false);
            $table->boolean('is_revised')->default(false);
            $table->integer('revised_by')->nullable();
            $table->dateTime('revised_at')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('capexes');
    }
}
