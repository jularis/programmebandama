<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountBaseController;

use App\Http\Helpers\Reply;
use App\Http\Requests\LeaveType\StoreLeaveType;
use App\Models\BaseModel;
use App\Models\Designation;
use App\Models\LeaveSetting;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Http\Request;

class LeaveTypeController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projectSettings';
        $this->activeSettingMenu = 'project_settings';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->teams = Team::all();
        $this->designations = Designation::allDesignations(); 

        return view('manager.leave-settings.create-leave-setting-type-modal', $this->data);
    }

    /**
     * @param StoreLeaveType $request
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreLeaveType $request)
    {
         
        $leaveType = new LeaveType();
        $leaveType->type_name = $request->type_name;
        $leaveType->cooperative_id = auth()->user()->cooperative_id;
        $leaveType->color = $request->color ?? '#000000';
        $leaveType->paid = $request->paid;
        $leaveType->no_of_leaves = $request->leave_number;
        $leaveType->monthly_limit = $request->monthly_limit;
        $leaveType->effective_after = $request->effective_after;
        $leaveType->effective_type = $request->effective_type;
        $leaveType->unused_leave = $request->unused_leave;
        $leaveType->encashed = $request->has('encashed') ? 1 : 0;
        $leaveType->allowed_probation = $request->has('allowed_probation') ? 1 : 0;
        $leaveType->allowed_notice = $request->has('allowed_notice') ? 1 : 0;
        $leaveType->gender = $request->gender ? json_encode($request->gender) : null;
        $leaveType->marital_status = $request->marital_status ? json_encode($request->marital_status) : null;
        $leaveType->department = $request->department ? json_encode($request->department) : null;
        $leaveType->designation = $request->designation ? json_encode($request->designation) : null;
        $leaveType->role = $request->role ? json_encode($request->role) : null;
        $leaveType->save();

        $leaveTypes = LeaveType::get();

        $options = BaseModel::options($leaveTypes, $leaveType, 'type_name');
        $notify[] = ['success', 'Le congé a été crée avec succès.'];
        return back()->withNotify($notify);
        // return Reply::successWithData(__('messages.leaveTypeAdded'), ['data' => $options, 'page_reload' => $request->page_reload]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
        $leaveType = LeaveType::find($id);
        $this->allTeams = Team::all();
        $this->allDesignations = Designation::allDesignations(); 
        $this->allGenders = ['male', 'female'];
        $this->allMaritalStatus = ['married', 'unmarried']; 
        $this->gender = json_decode($leaveType->gender);
       
        $this->maritalStatus = json_decode($leaveType->marital_status);
        $this->department = json_decode($leaveType->department);
        $this->designation = json_decode($leaveType->designation);
        $this->leaveType = $leaveType;
       
        return view('manager.leave-settings.edit-leave-setting-type-modal', $this->data);
    }

    public function update(StoreLeaveType $request, $id)
    {

        // if ($request->leaves < 0) {
        //     return Reply::error('messages.leaveTypeValueError');
        // }

        $leaveType = LeaveType::findOrFail($id);
        $leaveType->type_name = $request->type_name;
        $leaveType->color = $request->color;
        $leaveType->paid = $request->paid;
        $leaveType->no_of_leaves = $request->leave_number;
        $leaveType->monthly_limit = $request->monthly_limit;
        $leaveType->effective_after = $request->effective_after;
        $leaveType->effective_type = $request->effective_type;
        $leaveType->unused_leave = $request->unused_leave;
        $leaveType->encashed = $request->encashed;
        $leaveType->allowed_probation = $request->allowed_probation;
        $leaveType->allowed_notice = $request->allowed_notice;
        $leaveType->gender = $request->gender ? json_encode($request->gender) : null;
        $leaveType->marital_status = $request->marital_status ? json_encode($request->marital_status) : null;
        $leaveType->department = $request->department ? json_encode($request->department) : null;
        $leaveType->designation = $request->designation ? json_encode($request->designation) : null;
        $leaveType->role = $request->role ? json_encode($request->role) : null;
        $leaveType->save();
        $notify[] = ['success', 'Le type de congés a été crée avec succès.'];
         
        //return Reply::success(__('messages.leaveTypeAdded'));
            return redirect('/manager/settings/leaves-settings')->withNotify($notify);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        LeaveType::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
