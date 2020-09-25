<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['*'];


    public function division()
    {
    	return $this->belongsTo('App\Division');
    }

    public function user()
    {
        return $this->hasMany('App\User');
    }

    public function scopeGetDepartmentByCode($query, $department)
    {
        return $query->where('department_code', $department)->first();
    }

    public function approval()
    {
        return $this->hasOne('App\Approval', 'department', 'department_code');
    }
}
