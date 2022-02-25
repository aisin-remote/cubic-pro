<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DmaterialRb extends Model
{
	protected $table = 'direct_material_request_budgets';
	protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];

}