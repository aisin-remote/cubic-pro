<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Approval;
use App\ApprovalDtl;
use DataTables;
use DB;

use App\User;
use App\UserData;
use App\Role;
use App\Division;
use App\Department;
use App\System;


class ManageApprovalController extends Controller
{
	public $arr_dummy = [['id' => '', 'text' => '']];

    public function index(Request $request)
    {

    	$approval = Approval::get();
    	if ($request->wantsJson()) {
            return response()->json($approval, 200);
        }

        return view('pages.manage_approval.index');
    }
    public function create()
    {
    	$approval 	= Approval::get();
    	$department = Department::get();
    	$users 		= User::get();
		$role      = Role::get();
		$level_approval = System::configmultiply('level_approval');

    	return view('pages.manage_approval.create', compact(['approval','users','role','department','level_approval'])) ;
    }

    public function store(Request $request)
    {
    	$ret = DB::transaction(function() use ($request){

			$res = [
					'title' => 'Succses',
					'type' => 'success',
					'message' => 'Data Saved Success!'
				];

			try{
				$level 						= $request->level_approval;
				$user 						= $request->user;

				$approval = new Approval;
				$approval->department       = $request->department;
				$approval->is_seq       	= $request->is_seq;
				$approval->is_must_all      = $request->is_must_all;
				$approval->total_approval   = count($level);
				$approval->save();

				if (count($level) > 0 && count($user) > 0) {
					for ($i = 0; $i < count($level); $i++) {

							if ($level[$i] != "" && $user[$i] != "") {

								$approval_d 				= new ApprovalDtl;
								$approval_d->approval_id	= $approval->id;
								$approval_d->level       	= $level[$i];
								$approval_d->user_id 		= $user[$i];

								$approval_d->save();
							}else{
								throw new \Exception("There is empty data", 1);
							}

					}
				}else{

					throw new \Exception("There is empty data ---", 1);
				}

			}catch(\Exception $e){
				 DB::rollback();
				 $res = [
					'title' => 'Error',
					'type' => 'error',
					'message' => $e->getMessage(),
				];
			}

			return $res;
		 });

       return redirect()
                ->route('manage_approval.index')
                ->with($ret);


    }

    public function getData()
    {
    	$approval = Approval::select('*','approvals.id as approval_id')->join('departments', 'approvals.department','=','departments.department_code')->get();
        return DataTables::of($approval)
        ->rawColumns(['options'])

        ->addColumn('options', function($approval){
            return '
                <a href="'.route('manage_approval.edit', $approval->approval_id).'" class="btn btn-success btn-xs" data-toggle="tooltip" title="Edit"><i class="mdi mdi-pencil"></i></a>
                <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" onclick="on_delete('.$approval->approval_id.')"><i class="mdi mdi-close"></i></button>
                <form action="'.route('manage_approval.destroy', $approval->approval_id).'" method="POST" id="form-delete-'.$approval->approval_id .'" style="display:none">
                    '.csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            ';
        })

        ->toJson();
    }

    public function edit($id)
    {
        $approval 		= Approval::find($id);
		$approval_dtl 	= ApprovalDtl::where('approval_id',$id)->orderBy('level','ASC')->get();
    	$users 			= User::get();
    	$role      		= Role::get();
		$department 	= Department::get();
        return view('pages.manage_approval.edit', compact(['approval','approval_dtl','users','role','department']));
    }

    public function update(Request $request)
    {

		$ret = DB::transaction(function() use ($request){
					// try{
						$res = [
								'title' => 'Succses',
								'type' => 'success',
								'message' => 'Data Saved Success!'
							];
						$level = !empty($request->level_approval) ? array_merge($request->level, $request->level_approval) : $request->level;
						$approval = Approval::find($request->approval_id);

						if (empty($approval)) {
							return response()->json('Type not found', 500);
						}

						// $approval->department 	= $request->department;
						$approval->is_seq 		= $request->is_seq;
						$approval->is_must_all 	= $request->is_must_all;
						$approval->total_approval = count($level);
						$approval->save();

						ApprovalDtl::where('approval_id',$approval->id)->delete();


						foreach ($request->user as $i => $value){
							$approval_dtl = new ApprovalDtl();
							$approval_dtl->approval_id 	= $approval->id;
							$approval_dtl->user_id 		= $request->user[$i];
							$approval_dtl->level 		= $level[$i];

							$approval_dtl->save();
						}
					// }catch(\Exception $e){
					// 	 DB::rollback();
					// 	 $res = [
					// 		'title' => 'Error',
					// 		'type' => 'error',
					// 		'message' => $e->getMessage(),
					// 	];
					// }

				});

		if ($request->wantsJson()) {
			return response()->json($approval);
		}
        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data berhasil diubah!'
                ];

        return redirect()
                ->route('manage_approval.index')
                ->with($res);

    }

    public function destroy(Request $request, $id)
    {
        $approval = Approval::find($id);

        if (empty($approval)) {
            return response()->json('Type not found', 500);
        }

        $approval->delete();

        if ($request->wantsJson()) {
            return response()->json('Type deleted', 200);
        }

        $res = [
                    'title' => 'Sukses',
                    'type' => 'success',
                    'message' => 'Data Deleted Success!'
                ];

        return redirect()
                ->route('manage_approval.index')
                ->with($res);
    }
    public function getUser(Request $request)
    {
        $data = User::select('id', 'name as text')
                        ->get()
                        ->toArray();

        return response()->json(array_merge($this->arr_dummy, $data));
	}

	public function getLevel(Request $request)
    {
        $data = System::ConfigMultiply('level_approval');

        return response()->json(array_merge($this->arr_dummy, $data->toArray()));
    }

}
