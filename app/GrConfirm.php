<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrConfirm extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];

    public function approval_master()
    {
        return $this->belongsTo('App\ApprovalMaster', 'approval_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id', 'id');
    }

    public function details()
    {
    	return $this->hasMany(\App\GrConfirmDetail::class);
    }
}
