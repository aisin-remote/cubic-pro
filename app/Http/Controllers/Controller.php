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

	public function can_approve($approval_number)
	{
		$approvalMaster = ApprovalMaster::where('approval_number', $approval_number)->first();
		$approverUser = $approvalMaster->approverUsers()
			->where('user_id', auth()->user()->id)
			->where('is_approve', '0')
			->first();

		$can = 0;

		if ($approverUser) {
			$status = $approvalMaster->status == $approverUser->approvalDetail->status_to_approve;

			if ($status) {
				$can = 1;
			}
		}

		return $can;
	}
}
