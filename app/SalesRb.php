<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class SalesRb extends Model
{
	protected $table = 'sales_request_budgets';
	protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];


	protected static function getSalesElektrikDetail($payload=[])
	{
		$saleselectrik = SalesRb::select(
            'acc_name',
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
            ->where('group', 'Electric')
            ->groupBy('acc_code')
            ->get();
		return $saleselectrik;
	}

	protected static function getSalesUnitDetail($payload=[]){
		$salesunit = SalesRb::select(
			'acc_name',
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
            ->where('group', 'unit')
            ->groupBy('acc_code')
            ->get();
		return $salesunit;
	}

	protected static function getSalesBodyDetail($payload=[]){
		$salesbody = SalesRb::select(
			'acc_name',
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
		return $salesbody;
	}

	// bydept
	protected static function getSalesElektrikDept($payload=[])
	{
		$saleselectrik = SalesRb::select(
            'dept',
            'acc_name',
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
            ->where('group', 'Electric')
            ->groupBy('dept','acc_code')
            ->get();
		return $saleselectrik;
	}

	protected static function getSalesUnitDept($payload=[]){
		$salesunit = SalesRb::select(
            'dept',
            'acc_name',
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
            ->where('group', 'unit')
            ->groupBy('dept','acc_code')
            ->get();

		return $salesunit;
	}

	protected static function getSalesBodyDept($payload=[]){
		$salesbody = SalesRb::select(
            'dept',
            'acc_name',
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

		return $salesbody;
	}

}