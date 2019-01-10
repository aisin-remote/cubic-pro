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
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Plastic Material');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        } 
    }
    
    public function scopeSumPlasticMaterialTotal1($query, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Plastic Material');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumIngotMaterial($query, $month, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Ingot Material');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        } 
    }
    
    public function scopeSumIngotMaterialTotal1($query, $year, $product_code)
    {
        
       $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Ingot Material');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumCKD($query, $month, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'CKD');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
    }
    
    public function scopeSumCKDTotal1($query, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'CKD');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');

        
    }
    
     public function scopeSumCKDImportDuty($query, $month, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'CKD Import Duty');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
    }
    
    public function scopeSumCKDImportDutyTotal1($query, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'CKD Import Duty');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumImportPart($query, $month, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Import Part');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
    }
    
    public function scopeSumImportPartTotal1($query, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Import Part');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');

        
    }
   
    public function scopeSumLocalPart($query, $month, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Local Part');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
    }
    
    public function scopeSumLocalPartTotal1($query, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Local Part');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumInklaringImportPart($query, $month, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Inklaring Import Part');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
    }
    
    public function scopeSumInklaringImportPartTotal1($query, $year, $product_code)
    {
        
         $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Inklaring Import Part');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');
    }
    
    public function scopeSumInklaringCkd($query, $month, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Inklaring CKD');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
    }
     
    public function scopeSumInklaringCkdTotal1($query, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Inklaring CKD');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumImportDuty($query, $month, $year, $product_code)
    {
        
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Import Part Import Duty');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
    }
    
    public function scopeSumImportDutyTotal1($query, $year, $product_code)
    {
        
       $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code)
                                ->where('group_material', 'Import Part Import Duty');
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');

        
    }
    

    public function scopeSumTotalMaterial($query, $month, $year, $product_code)
    {

        
        // $result = $query->whereHas('bom.parts', function($where) use ($product_code) {
        //                 $where->where('product_code', $product_code);
        //             })
                    
        //             ->where('fiscal_year', $year)
        //             ->get();
         // $result = $query->whereHas('parts', function($where) use ($product_code) {
        //                 $where->where('product_code', $product_code);

        //             })
        //             ->join('boms', 'boms.part_id', '=', 'sales_datas.part_id')
        //             ->join('bom_datas', 'bom_datas.bom_id', '=', 'boms.id')
        //             ->join('parts', 'parts.id', '=', 'bom_datas.part_id')
        //             ->join('master_prices', 'master_prices.part_id', 'parts.id')           
        //             ->where('master_prices.source', '=', 'bom_datas.source')
        //             // ->groupBy('parts.product_code')
        //             ->where('fiscal_year', $year)
        //             ->get();
        
        // $part = Part::where('product_code', $product_code)
        //                 ->whereHas('price', function($where) use ($year) {
        //                     $where->where('fiscal_year',$year);
        //                 })
        //                 ->whereHas('bom', function($where) use ($year) {
        //                     $where->where('fiscal_year',$year);
        //                 })
        //                 ->first();

        // $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        // $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  

        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code);
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first();
    
        if ($month == 'apr') {
            
            return $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));

        } 
        elseif ($month == 'may') {

            return $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'june') {

            return $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'july') {

            return $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));;
        }
        elseif ($month == 'august') {

            return $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }
        elseif ($month == 'sep') {

            return $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'okt') {

            return $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'nov') {

            return $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'dec') {

            return $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'jan') {

            return $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'feb') {

            return $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }elseif ($month == 'march') {

            return $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'));
        }    
    }
    
    public function scopeSumTotalMaterial1($query, $year, $product_code)
    {
        $sales_data = $query->with([
                        'parts',
                        'parts.bom',
                        'parts.bom.details' => function($q) use ($product_code) {
                            $q->whereHas('parts', function($where) use ($product_code){
                                $where->where('product_code', $product_code);
                            });
                        },
                        'parts.bom.details.parts',
                        'parts.price'
                    ])
                    ->where('fiscal_year', $year)
                    ->first(); 
         

        $data[] = ['total' => $sales_data->apr_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->may_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->june_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->july_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->august_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->sep_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->oct_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->nov_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->dec_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->jan_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->feb_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];
        $data[] = ['total' => $sales_data->march_qty * ($sales_data->parts->bom->details->sum('qty') * $sales_data->parts->price->sum('price'))];

        return collect($data)->sum('total');

        // return json_encode($data);
    }

    

}