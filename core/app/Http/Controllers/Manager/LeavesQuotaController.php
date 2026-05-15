<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountBaseController;
use Carbon\Carbon;

use App\Models\User;
use App\Http\Helpers\Reply;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Models\EmployeeLeaveQuota;


class LeavesQuotaController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.leaves'; 
    }

    public function update(Request $request, $id)
    {
        if ($request->leaves < 0) {
            return Reply::error('messages.leaveTypeValueError');
        }

        $type = EmployeeLeaveQuota::findOrFail($id);
        $type->no_of_leaves = $request->leaves;
        $type->save();

        session()->forget('user');

        return Reply::success(__('messages.leaveTypeAdded'));
    }

    public function employeeLeaveTypes($userId)
    {
       
        if ($userId != 0) {
            $user = User::where('id',$userId)->first();
            
            $leaveQuotas = LeaveType::select('leave_types.*')->where('cooperative_id', $user->cooperative_id)->get(); 
           
            $options = '';

            foreach($leaveQuotas as $leave){

                        $options .= '<option value="' . $leave->id . '"> ' .  $leave->type_name . ' </option>'; 
            }
        }
        else {
            $leaveQuotas = LeaveType::all();

            $options = '';

            foreach ($leaveQuotas as $leaveQuota) {
                $options .= '<option value="' . $leaveQuota->id . '"> ' .  $leaveQuota->type_name . ' </option>';
            }
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $options]);
    }

}
