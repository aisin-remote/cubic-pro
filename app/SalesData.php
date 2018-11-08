<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SalesData extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];
     public function parts()
    {
        return $this->belongsTo('App\Part', 'part_id', 'id');
    }
     public function customers()
    {
        return $this->belongsTo('App\Customer','customer_id', 'id');
    }

    public function scopeSumSales($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code);
                    })
                    ->where('fiscal_year', $year);

        // return $result->sum('jan_amount');

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
    public function scopeSumSalesTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumSalesTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code);
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumSalesTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
     public function scopeSumPlasticMaterial($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Plastic Material');
                    })
                    ->where('fiscal_year', $year);

        // return $result->sum('jan_amount');

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
    public function scopeSumPlasticMaterialTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Plastic Material');
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumPlasticMaterialTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Plastic Material');
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumPlasticMaterialTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount +  may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Plastic Material');
                    })
                    ->where('fiscal_year', $year)
                    
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumIngotMaterial($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Ingot Material');
                    })
                    ->where('fiscal_year', $year);


        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
    public function scopeSumIngotMaterialTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Ingot Material');
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumIngotMaterialTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Ingot Material');
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumIngotMaterialTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount +  may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Ingot Material');
                    })
                    ->where('fiscal_year', $year)
                    
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumCKD($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'CKD');
                    })
                    ->where('fiscal_year', $year);

        // return $result->sum('jan_amount');

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
    public function scopeSumCKDTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'CKD');
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumCKDTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'CKD');
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumCKDTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount +  may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'CKD');
                    })
                    ->where('fiscal_year', $year)
                    
                    ->get();


        return $result->sum('total_amount');

        
    }
     public function scopeSumCKDImportDuty($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'CKD Import Duty');
                    })
                    ->where('fiscal_year', $year);

        // return $result->sum('jan_amount');

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
    public function scopeSumCKDImportDutyTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'CKD Import Duty');
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumCKDImportDutyTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'CKD Import Duty');
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumCKDImportDutyTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount +  may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'CKD Import Duty');
                    })
                    ->where('fiscal_year', $year)
                    
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumImportPart($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Import Part');
                    })
                    ->where('fiscal_year', $year);

        // return $result->sum('jan_amount');

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
    public function scopeSumImportPartTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Import Part');
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumImportPartTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Import Part');
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumImportPartTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount +  may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Import Part');
                    })
                    ->where('fiscal_year', $year)
                    
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumLocalPart($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Local Part');
                    })
                    ->where('fiscal_year', $year);

        // return $result->sum('jan_amount');

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
    public function scopeSumLocalPartTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Local Part');
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumLocalPartTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Local Part');
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumLocalPartTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount +  may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Local Part');
                    })
                    ->where('fiscal_year', $year)
                    
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumInklaringImportPart($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Inklaring Import Part');
                    })
                    ->where('fiscal_year', $year);

        // return $result->sum('jan_amount');

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
    public function scopeSumInklaringImportPartTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Inklaring Import Part');
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumInklaringImportPartTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Inklaring Import Part');
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumInklaringImportPartTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount +  may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Inklaring Import Part');
                    })
                    ->where('fiscal_year', $year)
                    
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumInklaringCkd($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Inklaring CKD');
                    })
                    ->where('fiscal_year', $year);

        // return $result->sum('jan_amount');

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
     public function scopeSumInklaringCkdTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Inklaring CKD');
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumInklaringCkdTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Inklaring CKD');
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumInklaringCkdTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount +  may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Inklaring CKD');
                    })
                    ->where('fiscal_year', $year)
                    
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumImportDuty($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Import Part Import Duty');
                    })
                    ->where('fiscal_year', $year);

        // return $result->sum('jan_amount');

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }


        // $result_sum = 0;

        // return $result_sum;
    }
    public function scopeSumImportDutyTotal($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Import Part Import Duty');
                    })
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('june_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('july_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('august_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');
        }elseif ($month == 'okt') {

            return $result->sum('okt_amount');
        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');
        }elseif ($month == 'dec') {

            return $result->sum('des_amount');
        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');
        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');
        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }
    }
    public function scopeSumImportDutyTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Import Part Import Duty');
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumImportDutyTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount +  may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                    $where->where('group_material', 'Import Part Import Duty');
                    })
                    ->where('fiscal_year', $year)
                    
                    ->get();


        return $result->sum('total_amount');

        
    }
}