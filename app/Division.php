<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    
    protected $fillable = ['*'];

    public function department()
    {
    	return $this->hasMany('App\Department');
    }

    public function scopeGetDivisionByCode($query, $division)
    {
        return $query->where('division_code', $division)->first();
    }
}
