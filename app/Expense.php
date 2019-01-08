<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
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
    public static function get_view_data($budget_no)
    {
        $data['data'] = [];
        if ($capex = self::query()->where('budget_no', '=', $budget_no)->first()) {
            foreach ($capex->approval_details as $approval_detail) {
                $data['data'][] = [
                $approval_detail->master->approval_number,
                $approval_detail->project_name,
                $approval_detail->BudgetReservedFormatted,
                $approval_detail->ActualPriceFormatted,
                $approval_detail->actual_qty,
                $approval_detail->BudgetStatus,
                $approval_detail->master->StatusFormatted,
                $approval_detail->ActualGrFormatted
                ];
            }
        }

        return $data;
    }
}
