<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\MasterPrice;
use App\Part;
use App\Bom;
use App\BomData;

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
        $result = DB::table('v_sales_datas')
                    ->where('product_code', $product_code)
                    ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('apr_amount');

        } elseif ($month == 'may') {

            return $result->sum('may_amount');
        }
        elseif ($month == 'june') {

            return $result->sum('jun_amount');
        }
        elseif ($month == 'july') {

            return $result->sum('jul_amount');
        }
        elseif ($month == 'august') {

            return $result->sum('aug_amount');
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_amount');

        }elseif ($month == 'okt') {

            return $result->sum('oct_amount');

        }elseif ($month == 'nov') {

            return $result->sum('nov_amount');

        }elseif ($month == 'dec') {

            return $result->sum('dec_amount');

        }elseif ($month == 'jan') {

            return $result->sum('jan_amount');

        }elseif ($month == 'feb') {

            return $result->sum('feb_amount');

        }elseif ($month == 'march') {

            return $result->sum('mar_amount');
        }elseif ($month == 'total') {

            return $result->sum('total');

        }
 
    }
    
    public function scopeSumSalesTotal($query, $month, $year, $product_code)
    {
        $result = DB::table('v_sum_sales_data')
                        ->where('fiscal_year', $year);

        if ($month == 'apr') {
            
            return $result->sum('sum_apr');

        } elseif ($month == 'may') {

            return $result->sum('sum_may');
        }
        elseif ($month == 'june') {

            return $result->sum('sum_jun');
        }
        elseif ($month == 'july') {

            return $result->sum('sum_jul');
        }
        elseif ($month == 'august') {

            return $result->sum('sum_aug');
        }
        elseif ($month == 'sep') {

            return $result->sum('sum_sep');

        }elseif ($month == 'okt') {

            return $result->sum('sum_oct');

        }elseif ($month == 'nov') {

            return $result->sum('sum_nov');

        }elseif ($month == 'dec') {

            return $result->sum('sum_dec');

        }elseif ($month == 'jan') {

            return $result->sum('sum_jan');

        }elseif ($month == 'feb') {

            return $result->sum('sum_feb');

        }elseif ($month == 'march') {

            return $result->sum('sum_mar');

        }elseif ($month == 'total') {

            return $result->sum('total');
        }
    }
    public function scopeSumSalesTotal1($year, $product_code)
    {
		return 0;
        
    } 
	public function scopeSumSalesTotal2($year, $product_code)
    {
		return 0;
        
    }
	public function scopeSumTotalMaterial1($year, $product_code)
    {
		return 0;
        
    }
	public function scopeSumPlasticMaterial($month,$year,$product_code)
	{
		return 0;
	}
	public function scopeSumPlasticMaterialTotal1($month,$year,$product_code)
	{
		return 0;
	}
    public function scopeMaterialGroup($query, $group, $month, $year, $product_code) {
        
        $sales_data = DB::table('v_material_group')
                            ->where('group_material',$group)
                            ->where('product_code', $product_code)
                            ->where('fiscal_year', $year)
                            ->first();

        if ($month == 'apr') {

            return !empty($sales_data->apr_amount) ? $sales_data->apr_amount : 0;

        } 
        elseif ($month == 'may') {

            return !empty($sales_data->may_amount) ? $sales_data->may_amount : 0;
        }
        elseif ($month == 'june') {

            return !empty($sales_data->jun_amount) ? $sales_data->jun_amount : 0;
        }
        elseif ($month == 'july') {

            return !empty($sales_data->jul_amount) ? $sales_data->jul_amount : 0;
        }
        elseif ($month == 'august') {

            return !empty($sales_data->aug_amount) ? $sales_data->aug_amount : 0;
        }
        elseif ($month == 'sep') {

            return !empty($sales_data->sep_amount) ? $sales_data->sep_amount : 0;

        }elseif ($month == 'okt') {

            return !empty($sales_data->oct_amount) ? $sales_data->oct_amount : 0;

        }elseif ($month == 'nov') {

            return !empty($sales_data->nov_amount) ? $sales_data->nov_amount : 0;

        }elseif ($month == 'dec') {

            return !empty($sales_data->dec_amount) ? $sales_data->dec_amount : 0;

        }elseif ($month == 'jan') {

            return !empty($sales_data->jan_amount) ? $sales_data->jan_amount : 0;

        }elseif ($month == 'feb') {

            return !empty($sales_data->feb_amount) ? $sales_data->feb_amount : 0;

        }elseif ($month == 'march') {

            return !empty($sales_data->mar_amount) ? $sales_data->mar_amount : 0;

        }elseif ($month == 'total'){
            return !empty($sales_data->total) ? $sales_data->total : 0;
        } 

    }
       

    public function scopeSumTotalMaterial($query, $month, $year, $product_code)
    {

        $sales_data = DB::table('v_material_product')
                            ->where('product_code', $product_code)
                            ->where('fiscal_year', $year)
                            ->first();
        if ($month == 'apr') {
            
            return !empty($sales_data->apr_amount) ? $sales_data->apr_amount : 0;

        } 
        elseif ($month == 'may') {

            return !empty($sales_data->may_amount) ? $sales_data->may_amount : 0;
        }
        elseif ($month == 'june') {

            return !empty($sales_data->jun_amount) ? $sales_data->jun_amount : 0;
        }
        elseif ($month == 'july') {

            return !empty($sales_data->jul_amount) ? $sales_data->jul_amount : 0;
        }
        elseif ($month == 'august') {

            return !empty($sales_data->aug_amount) ? $sales_data->aug_amount : 0;
        }
        elseif ($month == 'sep') {

            return !empty($sales_data->sep_amount) ? $sales_data->sep_amount : 0;
        }elseif ($month == 'okt') {

            return !empty($sales_data->oct_amount) ? $sales_data->oct_amount : 0;
        }elseif ($month == 'nov') {

            return !empty($sales_data->nov_amount) ? $sales_data->nov_amount : 0;
        }elseif ($month == 'dec') {

            return !empty($sales_data->dec_amount) ? $sales_data->dec_amount : 0;
        }elseif ($month == 'jan') {

            return !empty($sales_data->jan_amount) ? $sales_data->jan_amount : 0;
        }elseif ($month == 'feb') {

            return !empty($sales_data->feb_amount) ? $sales_data->feb_amount : 0;
        }elseif ($month == 'march') {

            return !empty($sales_data->mar_amount) ? $sales_data->mar_amount : 0;

        }elseif ($month == 'total') {

            return !empty($sales_data->total) ? $sales_data->total : 0;
        }    
    }

    public function scopePercTotalMaterial($query, $month, $year, $product_code)
    {

        $sales_data = DB::table('v_presentage_material_product')
                            ->where('product_code', $product_code)
                            ->where('fiscal_year', $year)
                            ->first();
    
        if ($month == 'apr') {
            
            return !empty($sales_data->perc_apr) ? $sales_data->perc_apr : 0;

        } 
        elseif ($month == 'may') {

            return !empty($sales_data->perc_may) ? $sales_data->perc_may : 0;
        }
        elseif ($month == 'june') {

            return !empty($sales_data->perc_jun) ? $sales_data->perc_jun : 0;
        }
        elseif ($month == 'july') {

            return !empty($sales_data->perc_jul) ? $sales_data->perc_jul : 0;
        }
        elseif ($month == 'august') {

            return !empty($sales_data->perc_aug) ? $sales_data->perc_aug : 0;
        }
        elseif ($month == 'sep') {

            return !empty($sales_data->perc_sep) ? $sales_data->perc_sep : 0;
        }elseif ($month == 'okt') {

            return !empty($sales_data->perc_oct) ? $sales_data->perc_oct : 0;
        }elseif ($month == 'nov') {

            return !empty($sales_data->perc_nov) ? $sales_data->perc_nov : 0;
        }elseif ($month == 'dec') {

            return !empty($sales_data->perc_dec) ? $sales_data->perc_dec : 0;
        }elseif ($month == 'jan') {

            return !empty($sales_data->perc_jan) ? $sales_data->perc_jan : 0;
        }elseif ($month == 'feb') {

            return !empty($sales_data->perc_feb) ? $sales_data->perc_feb : 0;
        }elseif ($month == 'march') {

            return !empty($sales_data->perc_mar) ? $sales_data->perc_mar : 0;

        }elseif ($month == 'total') {

            return !empty($sales_data->total) ? $sales_data->total : 0;
        }    
    }

    public function scopeSumPercTotalMaterial($query, $month, $year)
    {
        $sales_data = DB::table('v_presentage_sum_material_to_sales')
                        ->where('fiscal_year', $year)
                        ->first();
                        // dd($result);
        if ($month == 'apr') {

            return !empty($sales_data->perc_apr) ? $sales_data->perc_apr : 0;

        } 
        elseif ($month == 'may') {

            return !empty($sales_data->perc_may) ? $sales_data->perc_may : 0;
        }
        elseif ($month == 'june') {

            return !empty($sales_data->perc_jun) ? $sales_data->perc_jun : 0;
        }
        elseif ($month == 'july') {

            return !empty($sales_data->perc_jul) ? $sales_data->perc_jul : 0;
        }
        elseif ($month == 'august') {

            return !empty($sales_data->perc_aug) ? $sales_data->perc_aug : 0;
        }
        elseif ($month == 'sep') {

            return !empty($sales_data->perc_sep) ? $sales_data->perc_sep : 0;
        }elseif ($month == 'okt') {

            return !empty($sales_data->perc_oct) ? $sales_data->perc_oct : 0;
        }elseif ($month == 'nov') {

            return !empty($sales_data->perc_nov) ? $sales_data->perc_nov : 0;
        }elseif ($month == 'dec') {

            return !empty($sales_data->perc_dec) ? $sales_data->perc_dec : 0;
        }elseif ($month == 'jan') {

            return !empty($sales_data->perc_jan) ? $sales_data->perc_jan : 0;
        }elseif ($month == 'feb') {

            return !empty($sales_data->perc_feb) ? $sales_data->perc_feb : 0;
        }elseif ($month == 'march') {

            return !empty($sales_data->perc_mar) ? $sales_data->perc_mar : 0;

        }elseif ($month == 'total') {

            return !empty($sales_data->total) ? $sales_data->total : 0;
        }  
    }

    public function scopePercGroupMaterial($query, $group, $month, $year)
    {
        $sales_data = DB::table('v_percentage_material_group')
                        ->where('group_material', $group)
                        ->where('fiscal_year', $year)
                        ->first();

        if ($month == 'apr') {

            return !empty($sales_data->perc_apr) ? $sales_data->perc_apr : 0;

        } 
        elseif ($month == 'may') {

            return !empty($sales_data->perc_may) ? $sales_data->perc_may : 0;
        }
        elseif ($month == 'june') {

            return !empty($sales_data->perc_jun) ? $sales_data->perc_jun : 0;
        }
        elseif ($month == 'july') {

            return !empty($sales_data->perc_jul) ? $sales_data->perc_jul : 0;
        }
        elseif ($month == 'august') {

            return !empty($sales_data->perc_aug) ? $sales_data->perc_aug : 0;
        }
        elseif ($month == 'sep') {

            return !empty($sales_data->perc_sep) ? $sales_data->perc_sep : 0;
        }elseif ($month == 'okt') {

            return !empty($sales_data->perc_oct) ? $sales_data->perc_oct : 0;
        }elseif ($month == 'nov') {

            return !empty($sales_data->perc_nov) ? $sales_data->perc_nov : 0;
        }elseif ($month == 'dec') {

            return !empty($sales_data->perc_dec) ? $sales_data->perc_dec : 0;
        }elseif ($month == 'jan') {

            return !empty($sales_data->perc_jan) ? $sales_data->perc_jan : 0;
        }elseif ($month == 'feb') {

            return !empty($sales_data->perc_feb) ? $sales_data->perc_feb : 0;
        }elseif ($month == 'march') {

            return !empty($sales_data->perc_mar) ? $sales_data->perc_mar : 0;

        }elseif ($month == 'total') {

            return !empty($sales_data->total) ? $sales_data->total : 0;
        }  
    }

    public function getAprAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getMayAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getJuneAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getJulyAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getAugustAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getSepAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getOktAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getNovAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getDesAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getJanAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getFebAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getMarAmountAttribute($value)
    {
        return number_format($value);
    }

    public function getAprQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getMayQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getJuneQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getJulyQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getAugustQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getSepQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getOktQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getNovQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getDesQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getJanQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getFebQtyAttribute($value)
    {
        return number_format($value);
    }

    public function getMarQtyAttribute($value)
    {
        return number_format($value);
    }
    
}