<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApproverUser extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = ['*'];
}
