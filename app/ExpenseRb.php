<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseRb extends Model
{
	protected $table = 'expense_request_budgets';
	protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];

}