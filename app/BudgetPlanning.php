<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class BudgetPlanning extends Model
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
     public function scopeSumBudget($query, $month, $year, $product_code)
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
    public function scopeSumBudgetTotal($query, $month, $year, $product_code)
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
    public function scopeSumBudgetTotal1($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code);
                    })
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
    public function scopeSumBudgetTotal2($query, $year, $product_code)
    {
        
        $result = $query->select(DB::raw('jan_amount + feb_amount + mar_amount + apr_amount + may_amount +june_amount + july_amount + august_amount + sep_amount + okt_amount + nov_amount + des_amount as total_amount'))
                    ->where('fiscal_year', $year)
                    ->get();


        return $result->sum('total_amount');

        
    }
}
