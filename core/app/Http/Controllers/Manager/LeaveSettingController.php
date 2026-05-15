<?php
namespace App\Http\Controllers\Manager;
use App\Models\Team;
use App\Models\LeaveType;

use App\Models\Department;
use App\Http\Helpers\Reply;
use App\Models\Designation;
use App\Models\LeaveSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AccountBaseController;

class LeaveSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.leaveTypeSettings';
        $this->activeSettingMenu = 'leave_settings'; 
    }

    public function index()
    {
        $this->leaveTypes = DB::table('leave_types')->where('cooperative_id',auth()->user()->cooperative_id)->get();
        $leavePermission = LeaveSetting::first();
        
        $tab = request('tab');
       
        switch ($tab) {
        case 'general': 
            $this->leavePermission = $leavePermission; 
            $this->view = 'manager.leave-settings.ajax.general'; 
                break;
        default:
            $this->departments = Team::all();
            $this->designations = Designation::all(); 
            $this->view = 'manager.leave-settings.ajax.type'; 
                break;
        }

        $this->activeTab = $tab ?: 'type';
        $this->tab = $tab;
     
        if (request()->ajax()) {
             
            
             $html = view($this->view, $this->data)->render(); 
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

       
   
        return view('manager.leave-settings.index', $this->data);
    }

    public function store(Request $request)
    {
        $setting = cooperative();
        $setting->leaves_start_from = $request->leaveCountFrom;
        $setting->year_starts_from = $request->yearStartFrom;
        $setting->save();

        return Reply::success(__('messages.updateSuccess'));
    }
 
    
    public function changePermission(Request $request)
    {
        $permission = LeaveSetting::findOrFail($request->id);
        $permission->manager_permission = $request->value;
        $permission->update();

        return Reply::success(__('messages.updateSuccess'));
    }

}
