<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $fillable = ['*'];

    public function items()
    {
    	return $this->hasMany('App\Item','item_category_id','id');
    }
}
