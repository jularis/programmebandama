<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\AccountBaseController;

use App\Http\Helpers\Reply;
use App\Http\Requests\EmployeeShift\StoreEmployeeShift;
use App\Models\AttendanceSetting;
use App\Models\Cooperative;
use App\Models\EmployeeShift;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeShiftController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.ticketTypes';
        $this->activeSettingMenu = 'ticket_types';
    }

    public function create()
    {
        return view('manager.employee-shifts.create', $this->data);
    }

    public function store(StoreEmployeeShift $request)
    {
        $setting = new EmployeeShift();
        $setting->shift_name = $request->shift_name;
        $setting->shift_short_code = $request->shift_short_code;
        $setting->color = $request->color ?? '#99C7F1';
        $setting->office_start_time = Carbon::createFromFormat('H:i', $request->office_start_time);
        $setting->office_end_time = Carbon::createFromFormat('H:i', $request->office_end_time);
        $setting->halfday_mark_time = Carbon::createFromFormat('H:i', $request->halfday_mark_time);
        $setting->late_mark_duration = $request->late_mark_duration;
        $setting->clockin_in_day = $request->clockin_in_day;
        $setting->office_open_days = json_encode($request->office_open_days);
        $setting->early_clock_in = $request->early_clock_in;
        $setting->save();
        session()->forget('attendance_setting');
        return Reply::success(__('messages.employeeShiftAdded'));
    }

    public function edit($id)
    {
        $employeeShift = EmployeeShift::findOrFail($id);
         $this->employeeShift = $employeeShift;
        
        $this->openDays = json_decode($employeeShift->office_open_days);
        return view('manager.employee-shifts.edit', $this->data);
    }

    public function destroy($id)
    {
        EmployeeShift::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

    public function setDefaultShift()
    {
        $this->cooperative->attendanceSetting->update([
            'default_employee_shift' => request()->shiftID
        ]);


        session()->forget('attendance_setting');
        return Reply::success(__('messages.updateSuccess'));
    }

    public function update(StoreEmployeeShift $request, $id)
    {
        
        $setting = EmployeeShift::findOrFail($id);
        $setting->shift_name = $request->shift_name;
        $setting->shift_short_code = $request->shift_short_code;
        $setting->color = $request->color ?? '#99C7F1';
        $setting->office_start_time = Carbon::createFromFormat('H:i', $request->office_start_time);
        $setting->office_end_time = Carbon::createFromFormat('H:i', $request->office_end_time);
        $setting->halfday_mark_time = isset($request->halfday_mark_time) ? Carbon::createFromFormat('H:i', $request->halfday_mark_time) : $setting->halfday_mark_time;
        $setting->late_mark_duration = $request->late_mark_duration;
        $setting->clockin_in_day = $request->clockin_in_day;
        $setting->office_open_days = json_encode($request->office_open_days);
        $setting->early_clock_in = $request->early_clock_in;
        $setting->save();
        session()->forget('attendance_setting');
        $notify[] = ['success', "L'horaire de travail a été crée avec succès."];
        return back()->withNotify($notify);
       //return Reply::success(__('messages.updateSuccess'));
    }

    public function index()
    {
        $this->weekMap = Holiday::weekMap();
        $this->employeeShifts = EmployeeShift::where('shift_name', '<>', 'Day Off')->get();
        $generalShift = Cooperative::with(['attendanceSetting', 'attendanceSetting.shift'])->first();
            $this->defaultShift = ($generalShift && $generalShift->attendanceSetting && $generalShift->attendanceSetting->shift) ? $generalShift->attendanceSetting->shift : '--';
        return view('manager.employee-shifts.index', $this->data);
    }

}
