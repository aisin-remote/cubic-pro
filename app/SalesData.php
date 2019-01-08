<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\MasterPrice;
use App\Part;

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

         $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
    
    public function scopeSumPlasticMaterialTotal1($query, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Plastic Material');
                        
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                       ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumIngotMaterial($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Ingot Material');
                    })
                    ->where('fiscal_year', $year);


         $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
    
    public function scopeSumIngotMaterialTotal1($query, $year, $product_code)
    {
        
       $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Ingot Material');
                        
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumCKD($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'CKD');
                    })
                    ->where('fiscal_year', $year);

         $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
    
    public function scopeSumCKDTotal1($query, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'CKD');
                        
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];
        
        return collect($data)->sum('total');

        
    }
    
     public function scopeSumCKDImportDuty($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'CKD Import Duty');
                    })
                    ->where('fiscal_year', $year);

         $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
    
    public function scopeSumCKDImportDutyTotal1($query, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'CKD Import Duty');
                        
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumImportPart($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Import Part');
                    })
                    ->where('fiscal_year', $year);

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
    
    public function scopeSumImportPartTotal1($query, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Import Part');   
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];

        return collect($data)->sum('total');

        
    }
   
    public function scopeSumLocalPart($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Local Part');
                    })
                    ->where('fiscal_year', $year);

         $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
    
    public function scopeSumLocalPartTotal1($query, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Local Part');
                        
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })

                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];

        // return $result->sum('total_amount');

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumInklaringImportPart($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Inklaring Import Part');
                    })
                    ->where('fiscal_year', $year);

         $part = Part::where('product_code', $product_code)       
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

       $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
    
    public function scopeSumInklaringImportPartTotal1($query, $year, $product_code)
    {
        
         $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Inklaring Import Part');
                        
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];

        return collect($data)->sum('total');
    }
    
    public function scopeSumInklaringCkd($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Inklaring CKD');
                    })
                    ->where('fiscal_year', $year);

         $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
     
    public function scopeSumInklaringCkdTotal1($query, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Inklaring CKD');
                    
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];

        return collect($data)->sum('total');

        
    }
    
    public function scopeSumImportDuty($query, $month, $year, $product_code)
    {
        
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code)
                        ->where('group_material', 'Import Part Import Duty');
                    })
                    ->where('fiscal_year', $year);

         $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
    
    public function scopeSumImportDutyTotal1($query, $year, $product_code)
    {
        
       $result = $query->whereHas('parts', function($where) use ($product_code) {
                    $where->where('product_code', $product_code)
                    ->where('group_material', 'Import Part Import Duty');
                        
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];

        return collect($data)->sum('total');

        
    }
    

    public function scopeSumTotalMaterial($query, $month, $year, $product_code)
    {
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code);
                    })
                    ->where('fiscal_year', $year)
                    ->get();
        
        $part = Part::where('product_code', $product_code)
                        ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;  
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  



        if ($month == 'apr') {
            
            return $result->sum('apr_qty')*$qty*$price;

        } elseif ($month == 'may') {

            return $result->sum('may_qty')*$qty*$price;
        }
        elseif ($month == 'june') {

            return $result->sum('june_qty')*$qty*$price;
        }
        elseif ($month == 'july') {

            return $result->sum('july_qty')*$qty*$price;
        }
        elseif ($month == 'august') {

            return $result->sum('august_qty')*$qty*$price;
        }
        elseif ($month == 'sep') {

            return $result->sum('sep_qty')*$qty*$price;
        }elseif ($month == 'okt') {

            return $result->sum('okt_qty')*$qty*$price;
        }elseif ($month == 'nov') {

            return $result->sum('nov_qty')*$qty*$price;
        }elseif ($month == 'dec') {

            return $result->sum('des_qty')*$qty*$price;
        }elseif ($month == 'jan') {

            return $result->sum('jan_qty')*$qty*$price;
        }elseif ($month == 'feb') {

            return $result->sum('feb_qty')*$qty*$price;
        }elseif ($month == 'march') {

            return $result->sum('mar_qty')*$qty*$price;
        }
    }
    
    public function scopeSumTotalMaterial1($query, $year, $product_code)
    {
        $result = $query->whereHas('parts', function($where) use ($product_code) {
                        $where->where('product_code', $product_code);
                    })
                    ->where('fiscal_year', $year)
                    ->get();

        $part = Part::where('product_code', $product_code)
                    ->whereHas('price', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->whereHas('bom', function($where) use ($year) {
                            $where->where('fiscal_year',$year);
                        })
                        ->first();

        $price = !empty($part->price) ? $part->price->sum('price') : 0;    
        $qty = !empty($part->bom->details) ? $part->bom->details->sum('qty') : 0;  
         

        $data[] = ['total' => $result->sum('apr_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('may_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('june_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('july_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('august_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('sep_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('okt_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('nov_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('des_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('jan_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('feb_qty') *$qty* $price];
        $data[] = ['total' => $result->sum('mar_qty') *$qty* $price];

        return collect($data)->sum('total');

        // return json_encode($data);
    }

    

}