<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapexRb extends Model
{
	protected $table = 'capex_request_budgets';
	protected $fillable = ['*'];
    // protected $hidden = ['created_at', 'updated_at'];

}