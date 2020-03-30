<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
class Capex extends Model
{
    protected $fillable = ['*'];
    protected $hidden = ['created_at', 'updated_at'];
	protected $table  = "capexes";
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

	public static function ability()
	{
		$user = auth()->user();

		if(\Entrust::hasRole('user')) {
			$capexs = self::where('department',$user->department->department_code);
		}else if (\Entrust::hasRole('department_head')) {
            $capexs = self::whereIn('department', [$user->department->department_code]);
        }else if (\Entrust::hasRole('gm')) {
            $capexs = self::where('division', $user->division->division_code);
        }else if (\Entrust::hasRole('director')) {
            $capexs = self::where('dir', $user->dir);
        }else{
			$capexs = self::query();
		}

		return $capexs;
    }

	public function toArray($flag = '', $source_id = '', $time = '')
    {
        $user = auth()->user();

        $array = parent::toArray();

        if ($flag == 'archiving') {
            $array['archived_by'] = $user->id;
            $array['archived_at'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        elseif ($flag == 'fyear_closing') {
            $array['original_id'] 		= $source_id;
            $array['period_closed_by'] 	= $user->id;
            $array['period_closed_at'] 	= $time;
            $array['created_at'] 		= Carbon::now()->format('Y-m-d H:i:s');
            $array['updated_at'] 		= Carbon::now()->format('Y-m-d H:i:s');
        }
        return $array;
    }

    public function approvalDetails()
    {
        return $this->hasMany('App\ApprovalDetail', 'budget_no', 'budget_no');
    }
}
