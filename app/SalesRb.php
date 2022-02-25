<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesRb extends Model
{
	protected $table = 'sales_request_budgets';
	protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];

}