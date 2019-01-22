<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $fillable = ['*'];

    public function item()
    {
    	return $this->hasMany('App\Item','id','item_category_id');
    }
}
