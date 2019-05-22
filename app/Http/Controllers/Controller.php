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
		$connection = config('database.connections');
		// print_r($connection);exit;
		$approval   = array();
		$approver  	= array();
		
		$user 		= auth()->user();		
		$master 	= ApprovalMaster::getSelf($approval_number);
		
		if(count($master) > 0){
			$approvaldtl = ApprovalDtl::where('user_id',$user->id)->orderBy('approval_id','ASC')->get();
			foreach($approvaldtl as $adtl){
				$approval   = Approval::where('id',$adtl->approval_id)->first();
				if(!empty($approval)){
					if($approval->is_seq == '1' && (($master->status + 1) == $adtl->level)){
						$approver 	= ApproverUser::where('approval_master_id',empty($master)?0:$master->id)->where('user_id',$user->id)->first();
						if(!empty($approver)){
							break;
						}
					}else{
						$approver 	= ApproverUser::where('approval_master_id',empty($master)?0:$master->id)->where('user_id',$user->id)->first();
						if(!empty($approver)){
							break;
						}
					}
				}
			}
		}
		
		if(!empty($approver) && !empty($approval)){
			if($approval->is_seq == '1'){
				$status = 1;
			}else if($approval->is_seq == '0'){
				$status = 2;
			}
		}else{
			$status = 0;
		}
		
		return $status;
	}
}
