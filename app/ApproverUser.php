<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApproverUser extends Model
{
    protected $hidden 	= ['created_at', 'updated_at'];
	protected $fillable = ['*'];
	public $timestamps 	= false;

	public function approvalDetail()
	{
		return $this->belongsTo('App\ApprovalDtl', 'approval_detail_id');
	}
}
