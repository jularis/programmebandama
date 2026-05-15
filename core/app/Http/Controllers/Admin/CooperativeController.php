<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeaveType;
use App\Models\Cooperative;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmployeeShift;
use App\Models\CustomFieldGroup;
use App\Models\AttendanceSetting;
use App\Http\Controllers\Controller;
use App\Models\GoogleCalendarModule;

class CooperativeController extends Controller
{

    public function index()
    {
        $pageTitle = "Gestion des coopératives";
        $cooperatives  = Cooperative::searchable(['codeCoop','codeApp','name', 'email', 'phone', 'address'])->orderBy('name', 'DESC')->paginate(getPaginate());
        return view('admin.cooperative.index', compact('pageTitle', 'cooperatives'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|max:255',
            'address' => 'required|max:255',
            'web' => 'required|max:255',
            'mobile' => 'required|max:255',
            'color' => 'required|max:255',
        ]);
        
        if ($request->id) {
            $cooperative  = Cooperative::findOrFail($request->id);
            $message = "La coopérative a été mise à jour avec succès";
        } else {
            $cooperative  = new Cooperative();
            $message = "La coopérative a été ajoutée avec succès";
        }
 
        $cooperative->codeCoop    = $request->codeCoop;
        $cooperative->name    = $request->name;
        $cooperative->email   = $request->email;
        $cooperative->phone   = $request->phone;
        $cooperative->address = $request->address;
        $cooperative->web = $request->web;
        $cooperative->mobile = $request->mobile;
        $cooperative->color = '#'.$request->color;
        $cooperative->codeApp   = isset($request->codeApp) ? $request->codeApp : $this->generecodeapp($request->name); 
        $cooperative->save();

        if(!$request->id)
        {
            $this->cooperativeAddress($cooperative);
            $this->employeeShift($cooperative);
            $this->attendanceSetting($cooperative);
            $this->customFieldGroup($cooperative);
            $this->leaveType($cooperative);
            $cooperative->leaveSetting()->create();
            $this->googleCalendar($cooperative);
        }
        

        $notify[] = ['success',$message];
        return back()->withNotify($notify);
    }

    private function generecodeapp($name)
    {

        $data = Cooperative::select('codeApp')->orderby('id','desc')->limit(1)->get();

        if(count($data)>0){
            $code = $data[0]->codeApp;

        $chaine_number = Str::afterLast($code,'-');

        if($chaine_number<10){$zero="00";}
        else if($chaine_number<100){$zero="0";}
        else{$zero="";}
        }else{
            $zero="00";
            $chaine_number=0;
        }


        $abrege=Str::upper(Str::substr($name,0,3));
        $sub=$abrege.'-';
        $lastCode=$chaine_number+1;
        $codeP=$sub.$zero.$lastCode;

        return $codeP;
    }

    public function status($id)
    {
        return Cooperative::changeStatus($id);
    }
    public function customFieldGroup($cooperative)
    {

        $fields = CustomFieldGroup::ALL_FIELDS;

        array_walk($fields, function (&$a) use ($cooperative) {
            $a['cooperative_id'] = $cooperative->id;
        });

        CustomFieldGroup::insert($fields);

    }
    public function cooperativeAddress($cooperative)
    {
        $cooperative->cooperativeAddress()->create([
            'address' => $cooperative->address ?? $cooperative->name,
            'location' => $cooperative->name ?? 'CCB',
            'is_default' => 1,
            'cooperative_id' => $cooperative->id,
        ]);
    }
    public function googleCalendar($cooperative): void
    {
        $module = new GoogleCalendarModule();
        $module->cooperative_id = $cooperative->id;
        $module->lead_status = 0;
        $module->leave_status = 0;
        $module->invoice_status = 0;
        $module->contract_status = 0;
        $module->task_status = 0;
        $module->event_status = 0;
        $module->holiday_status = 0;
        $module->saveQuietly();
    }
    public function employeeShift($cooperative)
    {

        $employeeShift = new EmployeeShift();
        $employeeShift->shift_name = 'Day Off';
        $employeeShift->cooperative_id = $cooperative->id;
        $employeeShift->shift_short_code = 'DO';
        $employeeShift->color = '#E8EEF3';
        $employeeShift->late_mark_duration = 0;
        $employeeShift->clockin_in_day = 0;
        $employeeShift->office_open_days = '';
        $employeeShift->saveQuietly();

        $employeeShift = new EmployeeShift();
        $employeeShift->shift_name = 'Horaires de Travail';
        $employeeShift->cooperative_id = $cooperative->id;
        $employeeShift->shift_short_code = 'HT';
        $employeeShift->color = '#99C7F1';
        $employeeShift->office_start_time = '08:00:00';
        $employeeShift->office_end_time = '18:00:00';
        $employeeShift->late_mark_duration = 20;
        $employeeShift->clockin_in_day = 2;
        $employeeShift->office_open_days = '["1","2","3","4","5"]';
        $employeeShift->saveQuietly();
    }

    public function attendanceSetting($cooperative)
    {
        $setting = new AttendanceSetting();
        $setting->cooperative_id = $cooperative->id;
        $setting->office_start_time = '09:00:00';
        $setting->office_end_time = '18:00:00';
        $setting->late_mark_duration = 20;
        $setting->default_employee_shift = EmployeeShift::where('cooperative_id', $cooperative->id)->where('shift_name', '<>', 'Day Off')->first()->id;
        $setting->alert_after_status = 0;
        $setting->saveQuietly();
    }

    public function leaveType($cooperative)
    {
        $gender = ['Homme','Femme'];
        $maritalstatus = ['marie','celibataire'];

        $status = [
            ['type_name' => 'Payes', 'color' => '#16813D', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Maladie', 'color' => '#DB1313', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Deuil', 'color' => '#B078C6', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Maternite', 'color' => '#B078C6', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => ''],
            ['type_name' => 'Parternite', 'color' => '#B078C6', 'cooperative_id' => $cooperative->id, 'gender' => json_encode($gender), 'marital_status' => json_encode($maritalstatus), 'role' => '']
        ];

        LeaveType::insert($status);

    }

}
