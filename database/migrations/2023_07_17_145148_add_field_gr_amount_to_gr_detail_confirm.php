<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldGrAmountToGrDetailConfirm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gr_confirm_details', function (Blueprint $table) {
            $table->decimal('gr_amount', 17, 2)->after('approval_detail_id')->nullable();
            $table->date('gr_date')->after('gr_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gr_confirm_details', function (Blueprint $table) {
            $table->dropColumn('gr_amount');
            $table->dropColumn('gr_date');
        });
    }
}
