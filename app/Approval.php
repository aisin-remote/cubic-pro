<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];

	public function details()
	{
		return $this->hasMany('App\ApprovalDtl', 'approval_id');
	}
}
