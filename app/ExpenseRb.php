<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ExpenseRb extends Model
{
	protected $table = 'expense_request_budgets';
	protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];

	protected static function getExpenseElektrikDetail($payload=[]){
		$expenseelectrik = ExpenseRb::select(
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'electric')
            ->groupBy('acc_code')
            ->get();

		return $expenseelectrik;
	}
	protected static function getExpenseUnitDetail($payload=[]){
		$expenseunit = ExpenseRb::select(
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'Unit')
            ->groupBy('acc_code')
            ->get();

		return $expenseunit;
        
	}
	protected static function getExpenseBodyDetail($payload=[]){
		$expensebody = ExpenseRb::select(
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'body')
            ->groupBy('acc_code')
            ->get();
		return $expensebody;
	}

	protected static function getExpenseElektrikDept($payload=[]){
		$expenseelectrik = ExpenseRb::select(
            'dept',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'electric')
            ->groupBy('dept','acc_code')
            ->get();
			return $expenseelectrik;
	}
	protected static function getExpenseUnitDept($payload=[]){
		$expenseunit = ExpenseRb::select(
            'dept',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'Unit')
            ->groupBy('dept','acc_code')
            ->get();
        return $expenseunit;
	}
	protected static function getExpenseBodyDept($payload=[]){
		$expensebody = ExpenseRb::select(
            'dept',
            'acc_code',
            DB::raw('ifnull(sum(april),0) as sapril'),
            DB::raw('ifnull(sum(mei),0) as smei'),
            DB::raw('ifnull(sum(juni),0) as sjuni'),
            DB::raw('ifnull(sum(juli),0) as sjuli'),
            DB::raw('ifnull(sum(agustus),0) as sagustus'),
            DB::raw('ifnull(sum(september),0) as sseptember'),
            DB::raw('ifnull(sum(oktober),0) as soktober'),
            DB::raw('ifnull(sum(november),0) as snovember'),
            DB::raw('ifnull(sum(december),0) as sdecember'),
            DB::raw('ifnull(sum(januari),0) as sjanuari'),
            DB::raw('ifnull(sum(februari),0) as sfebruari'),
            DB::raw('ifnull(sum(maret),0) as smaret')
        )
            ->where('group', 'body')
            ->groupBy('dept','acc_code')
            ->get();
		return $expensebody;
	}
	

}