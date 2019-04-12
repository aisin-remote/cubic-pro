<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Approval;
use App\ApproverUser;
use App\ApprovalDtl;
use App\ApprovalMaster;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function can_approve($approval_number){
		$user 		= auth()->user();
		$approval   = Approval::where('department',$user->department->department_code)->first();
		$master 	= ApprovalMaster::getSelf($approval_number);
		if(count($approval) > 0 && count($master) > 0){
			if($approval->is_seq == '1'){
				$approvaldtl = ApprovalDtl::where('approval_id',$approval->id)->where('user_id',$user->id)->first();
				if(!empty($approvaldtl)){
					if(($master->status + 1) == $approvaldtl->level){
						$approver 	= ApproverUser::where('approval_master_id',empty($master)?0:$master->id)->where('user_id',$user->id)->first();
					}else{
						$approver   = [];
					}
				}else{
					$approver = [];
				}
			}else{
				$approver 	= ApproverUser::where('approval_master_id',empty($master)?0:$master->id)->where('user_id',$user->id)->first();
			}
		}else{
			$approver = [];
		}
		if(!empty($approver) && $approval->is_seq == '1'){
			$status = 1;
		}else if(!empty($approver) && $approval->is_seq == '0'){
			$status = 2;
		}else{
			$status = 0;
		}
		return $status;
	}
}
