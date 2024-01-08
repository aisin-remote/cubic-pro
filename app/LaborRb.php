<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class LaborRb extends Model
{
    protected $table = 'labor_request_budgets';
    protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];

    protected static function getLaborElektrikDetail($payload=[]){
        $laborelectrik = LaborRb::select(
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

        return $laborelectrik;
    }
    protected static function getLaborUnitDetail($payload=[]){
        $laborunit = LaborRb::select(
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

        return $laborunit;

    }
    protected static function getLaborBodyDetail($payload=[]){
        $laborbody = LaborRb::select(
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

        return $laborbody;
    }
    protected static function getLaborElektrikDept($payload=[]){
        $laborelectrik = LaborRb::select(
            'dept',
            'acc_name',
            DB::raw('LEFT(code, 3) as acc_code'),
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
            ->groupBy('dept','code')
            ->get();

        return $laborelectrik;
    }
    protected static function getLaborUnitDept($payload=[]){
        $laborunit = LaborRb::select(
            'dept',
            'acc_name',
            DB::raw('LEFT(code, 3) as acc_code'),
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
            ->groupBy('dept','code')
            ->get();
        return $laborunit;
    }
    protected static function getLaborBodyDept($payload=[]){
        $laborbody = LaborRb::select(
            'dept',
            'acc_name',
            DB::raw('LEFT(code, 3) as acc_code'),
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
            ->groupBy('dept','code')
            ->get();
        return $laborbody;
    }
}
