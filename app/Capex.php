<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Capex extends Model
{
    protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];

    public function department()
    {
        return $this->belongsTo('App\Department', 'department_id', 'id');
    }
    public static function getByBudgetNo($budget_no)
    {
        return self::query()->where('budget_no', $budget_no)->first();
    }

}
