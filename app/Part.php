<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];

    public function price()
    {
    	return $this->hasOne('App\MasterPrice', 'part_id');
    }

     public function bom()
    {
    	return $this->hasOne('App\Bom', 'part_id');
    	
    }
}
