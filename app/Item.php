<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['*'];


    public function item_category()
    {
    	return $this->belongsTo('App\ItemCategory','item_category_id','id');	
    }

    public function uom()
    {
    	return $this->belongsTo('App\SapUom','uom_id','id');	
    }

    public function supplier()
    {
    	return $this->belongsTo('App\Supplier', 'supplier_id', 'id');	
    }
}
