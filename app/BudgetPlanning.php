<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
