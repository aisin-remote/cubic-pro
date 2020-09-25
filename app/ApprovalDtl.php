<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovalDtl extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];

	public function user()
    {
        return $this->belongsTo('App\User', 'id', 'user_id');
    }

    public function approval()
    {
        return $this->belongsTo('App\Approval', 'approval_id');
    }

    public function approverUsers()
    {
        return $this->hasMany('App\ApproverUser', 'approval_detail_id');
    }
}
