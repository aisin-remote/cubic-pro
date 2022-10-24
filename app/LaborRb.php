<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaborRb extends Model
{
    protected $table = 'labor_request_budgets';
    protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];
}
