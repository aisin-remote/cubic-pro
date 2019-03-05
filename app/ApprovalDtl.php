<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovalDtl extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];
	
	public function user()
    {
        return $this->hasMany('App\User', 'id', 'user_id');
    }
}
