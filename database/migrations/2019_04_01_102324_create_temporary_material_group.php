<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemporaryMaterialGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporary_material_groups', function (Blueprint $table) {
            $table->increments('id');
			$table->string('group_material',100)->nullable()->default(NULL);
            $table->string('product_code',20)->nullable()->default(NULL);
			$table->string('product_name',255)->nullable()->default(NULL);
			$table->string('fiscal_year',100)->nullable();
			$table->integer('apr_amount')->nullable()->default('0');
			$table->integer('may_amount')->nullable()->default('0');
			$table->integer('jun_amount')->nullable()->default('0');
			$table->integer('jul_amount')->nullable()->default('0');
			$table->integer('aug_amount')->nullable()->default('0');
			$table->integer('sep_amount')->nullable()->default('0');
			$table->integer('oct_amount')->nullable()->default('0');
			$table->integer('nov_amount')->nullable()->default('0');
			$table->integer('dec_amount')->nullable()->default('0');
			$table->integer('jan_amount')->nullable()->default('0');
			$table->integer('feb_amount')->nullable()->default('0');
			$table->integer('mar_amount')->nullable()->default('0');
			$table->integer('total')->nullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temporary_material_groups');
    }
}
