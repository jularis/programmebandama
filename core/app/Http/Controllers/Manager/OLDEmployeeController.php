<?php
namespace App\Http\Controllers\Manager;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Helpers\Files;
use App\Models\Countrie;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\EmployeeDetail;
use App\Models\module_permission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    // all employee card view
    public function cardAllEmployee(Request $request)
    {
        $users = EmployeeDetail::with('user')->get(); 
        $designations = Designation::get();
        $teams = Department::get();
        $countries = Countrie::get();
        return view('manager.form.allemployeecard',compact('users','designations','teams','countries'));
    }
    // all employee list
    public function listAllEmployee()
    {
        
        $users = EmployeeDetail::with('user')->get(); 
        $designations = Designation::get();
        $teams = Department::get();
        $countries = Countrie::get();
        return view('manager.form.employeelist',compact('users','designations','teams','countries'));
    }

    // save data employee
    public function saveRecord(Request $request)
    {

        $validationRule = [ 
            'nom' => 'required|max:255',
            'prenom'  => 'required|max:255',
            'email'  => 'required|max:255',
            'date_of_birth'  => 'required|max:255', 
            'designation'  => 'required|max:255',
            'department'  => 'required|max:255',
            'joining_date'  => 'required|max:255', 
        ];
 

        $request->validate($validationRule); 

        if($request->id) {
            $employe = EmployeeDetail::findOrFail($request->id); 
            $message = "La employe a été mise à jour avec succès";

        } else {
            $employe = new EmployeeDetail();   
        } 
        $manager        = auth()->user();
         
        $employe->date_of_birth    = Carbon::parse($request->date_of_birth)->format('Y-m-d'); 
        $employe->designation_id  = $request->designation; 
        $employe->department_id  = $request->department; 
        $employe->country_id    = $request->country;
        $employe->employee_matricule = $request->matricule;
        $employe->country_phonecode = $request->country_phonecode;  
        $employe->joining_date     =  Carbon::parse($request->joining_date)->format('Y-m-d');
        $employe->reporting_to  = $request->reporting_to;
        $employe->address    = $request->address;
        $employe->about_me = $request->about_me; 
        $employe->employment_type    = $request->employment_type;
        $employe->internship_end_date     =  Carbon::parse($request->internship_end_date)->format('Y-m-d'); 
        $employe->contract_end_date     =  Carbon::parse($request->contract_end_date)->format('Y-m-d'); 
        $employe->marital_status     = $request->marital_status; 
        $employe->marriage_anniversary_date     =  Carbon::parse($request->marriage_anniversary_date)->format('Y-m-d'); 
        
        $staff = new User();
        $staff->cooperative_id = $manager->cooperative_id;
        $staff->firstname = $request->nom;
        $staff->lastname  = $request->prenom;
        $staff->username  = Str::limit(Str::slug($request->prenom,""),12,'.').Str::limit(Str::slug($request->nom,""),1,'');
        $staff->email     = $request->email;
        $staff->mobile    = $request->mobile;
        $staff->genre     = $request->gender;
        $staff->adresse    = "";
        $staff->user_type = "Employe"; 
        $staff->type_compte = "web"; 
        $staff->password  =  Hash::make('azerty'); 
        if($request->hasFile('image')) {
            Files::deleteFile($staff->image, 'avatar');
            $staff->image = Files::uploadLocalOrS3($request->image, 'avatar', 400);
        }
        $staff->save();
        if($staff !=null ){
            $role = DB::table('roles')->where('name','Employe')->first();
            if($role !=null)
            {
                $staff->syncRoles($role->id);
            }
            

            $id = $staff->id;
            $employe->cooperative_id  = $manager->cooperative_id ;
            $employe->user_id  = $staff->id;
            $employe->save(); 
            
        }

        $notify[] = ['success', isset($message) ? $message : 'Cet employé a été crée avec succès.'];
        return back()->withNotify($notify);
    }
    // view edit record
    public function viewRecord($employee_id)
    {
        $permission = DB::table('employees')
            ->join('module_permissions', 'employees.employee_id', '=', 'module_permissions.employee_id')
            ->select('employees.*', 'module_permissions.*')
            ->where('employees.employee_id','=',$employee_id)
            ->get();
        $employees = DB::table('employees')->where('employee_id',$employee_id)->get();
        return view('manager.form.edit.editemployee',compact('employees','permission'));
    }
    // update record employee
    public function updateRecord( Request $request)
    {
        DB::beginTransaction();
        try{
            // update table Employee
            $updateEmployee = [
                'id'=>$request->id,
                'name'=>$request->name,
                'email'=>$request->email,
                'birth_date'=>$request->birth_date,
                'gender'=>$request->gender,
                'employee_id'=>$request->employee_id,
                'cooperative'=>$request->cooperative,
            ];
            // update table user
            $updateUser = [
                'id'=>$request->id,
                'name'=>$request->name,
                'email'=>$request->email,
            ];
 
            User::where('id',$request->id)->update($updateUser);
            Employee::where('id',$request->id)->update($updateEmployee);
        
            DB::commit();
            Toastr::success('updated record successfully :)','Success');
            return redirect()->route('all/employee/card');
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('updated record fail :)','Error');
            return redirect()->back();
        }
    }
    // delete record
    public function deleteRecord($employee_id)
    {
        DB::beginTransaction();
        try{

            Employee::where('employee_id',$employee_id)->delete();
            module_permission::where('employee_id',$employee_id)->delete();

            DB::commit();
            Toastr::success('Delete record successfully :)','Success');
            return redirect()->route('all/employee/card');

        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('Delete record fail :)','Error');
            return redirect()->back();
        }
    }
    // employee search
    public function employeeSearch(Request $request)
    {
        $users = DB::table('users')
                    ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                    ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                    ->get();
        $permission_lists = DB::table('permission_lists')->get();
        $userList = DB::table('users')->get();

        // search by id
        if($request->employee_id)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('employee_id','LIKE','%'.$request->employee_id.'%')
                        ->get();
        }
        // search by name
        if($request->name)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('users.name','LIKE','%'.$request->name.'%')
                        ->get();
        }
        // search by name
        if($request->position)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('users.position','LIKE','%'.$request->position.'%')
                        ->get();
        }

        // search by name and id
        if($request->employee_id && $request->name)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('employee_id','LIKE','%'.$request->employee_id.'%')
                        ->where('users.name','LIKE','%'.$request->name.'%')
                        ->get();
        }
        // search by position and id
        if($request->employee_id && $request->position)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('employee_id','LIKE','%'.$request->employee_id.'%')
                        ->where('users.position','LIKE','%'.$request->position.'%')
                        ->get();
        }
        // search by name and position
        if($request->name && $request->position)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('users.name','LIKE','%'.$request->name.'%')
                        ->where('users.position','LIKE','%'.$request->position.'%')
                        ->get();
        }
         // search by name and position and id
         if($request->employee_id && $request->name && $request->position)
         {
             $users = DB::table('users')
                         ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                         ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                         ->where('employee_id','LIKE','%'.$request->employee_id.'%')
                         ->where('users.name','LIKE','%'.$request->name.'%')
                         ->where('users.position','LIKE','%'.$request->position.'%')
                         ->get();
         }
        return view('manager.form.allemployeecard',compact('users','userList','permission_lists'));
    }
    public function employeeListSearch(Request $request)
    {
        $users = DB::table('users')
                    ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                    ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                    ->get(); 
        $permission_lists = DB::table('permission_lists')->get();
        $userList = DB::table('users')->get();

        // search by id
        if($request->employee_id)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('employee_id','LIKE','%'.$request->employee_id.'%')
                        ->get();
        }
        // search by name
        if($request->name)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('users.name','LIKE','%'.$request->name.'%')
                        ->get();
        }
        // search by name
        if($request->position)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('users.position','LIKE','%'.$request->position.'%')
                        ->get();
        }

        // search by name and id
        if($request->employee_id && $request->name)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('employee_id','LIKE','%'.$request->employee_id.'%')
                        ->where('users.name','LIKE','%'.$request->name.'%')
                        ->get();
        }
        // search by position and id
        if($request->employee_id && $request->position)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('employee_id','LIKE','%'.$request->employee_id.'%')
                        ->where('users.position','LIKE','%'.$request->position.'%')
                        ->get();
        }
        // search by name and position
        if($request->name && $request->position)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('users.name','LIKE','%'.$request->name.'%')
                        ->where('users.position','LIKE','%'.$request->position.'%')
                        ->get();
        }
        // search by name and position and id
        if($request->employee_id && $request->name && $request->position)
        {
            $users = DB::table('users')
                        ->join('employees', 'users.user_id', '=', 'employees.employee_id')
                        ->select('users.*', 'employees.birth_date', 'employees.gender', 'employees.cooperative')
                        ->where('employee_id','LIKE','%'.$request->employee_id.'%')
                        ->where('users.name','LIKE','%'.$request->name.'%')
                        ->where('users.position','LIKE','%'.$request->position.'%')
                        ->get();
        }
        return view('manager.form.employeelist',compact('users','userList','permission_lists'));
    }

    // employee profile with all controller user
    public function profileEmployee($user_id)
    {
        $users = DB::table('users')
                ->leftJoin('personal_information','personal_information.user_id','users.user_id')
                ->leftJoin('profile_information','profile_information.user_id','users.user_id')
                ->where('users.user_id',$user_id)
                ->first();
        $user = DB::table('users')
                ->leftJoin('personal_information','personal_information.user_id','users.user_id')
                ->leftJoin('profile_information','profile_information.user_id','users.user_id')
                ->where('users.user_id',$user_id)
                ->get(); 
        return view('manager.form.employeeprofile',compact('user','users'));
    }

    /** page departments */
    public function index()
    {
        $pageTitle = "Départements";
        $departments = DB::table('departments')->get();
        
        return view('manager.form.departments',compact('departments','pageTitle'));
    }

    /** save record department */
    public function saveRecordDepartment(Request $request)
    {
        $request->validate([
            'department'        => 'required|string|max:255',
        ]);
 
        try{

            $department = department::where('department',$request->department)->first();
            if ($department === null)
            {
                $department = new department;
                $department->department = $request->department;
                $department->save();
    
                DB::commit();
                Toastr::success('Add new department successfully :)','Success');
                return redirect()->back();
            } else {
                DB::rollback();
                Toastr::error('Add new department exits :)','Error');
                return redirect()->back();
            }
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('Add new department fail :)','Error');
            return redirect()->back();
        }
    }

    /** update record department */
    public function updateRecordDepartment(Request $request)
    {
        DB::beginTransaction();
        try{
            // update table departments
            $department = [
                'id'=>$request->id,
                'department'=>$request->department,
            ];
            Department::where('id',$request->id)->update($department);
        
            DB::commit();
            Toastr::success('updated record successfully :)','Success');
            return redirect()->route('form/departments/page');
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('updated record fail :)','Error');
            return redirect()->back();
        }
    }

    /** delete record department */
    public function deleteRecordDepartment(Request $request) 
    {
        try {

            Department::destroy($request->id);
            Toastr::success('Department deleted successfully :)','Success');
            return redirect()->back();
        
        } catch(\Exception $e) {

            DB::rollback();
            Toastr::error('Department delete fail :)','Error');
            return redirect()->back();
        }
    }

    /** page designations */
    public function designationsIndex()
    {
        $pageTitle ="Désignations";
        $designations = Designation::get();
     
        return view('manager.form.designations', compact('pageTitle','designations'));
    }

    /** save record designation */
    public function saveRecordDesignations(Request $request)
    {
        $request->validate([
            'designation'        => 'required|string|max:255', 
        ]);
 
        try{

            $designation = Designation::where('name',$request->designation)->first();
            if ($designation === null)
            {
                $designation = new Designation;
                $designation->name = $request->designation; 
                $designation->save();
    
                DB::commit();
                Toastr::success('Add new désignation successfully :)','Success');
                return redirect()->back();
            } else {
                DB::rollback();
                Toastr::error('Add new désignation exits :)','Error');
                return redirect()->back();
            }
        }catch(\Exception $e){
            DB::rollback();
            Toastr::error('Add new désignation fail :)','Error');
            return redirect()->back();
        }
    }

    /** update record designation */
    public function updateRecordDesignations(Request $request)
    {
        DB::beginTransaction();
        try{
            // update table designation
            $designation = [
                'id'=>$request->id,
                'name'=>$request->designation,
            ];
            Designation::where('id',$request->id)->update($designation);
        
            DB::commit();
            Toastr::success('updated record successfully :)','Success');
            return redirect()->route('manager.hr.form.designations.page');
        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('updated record fail :)','Error');
            return redirect()->back();
        }
    }

    /** delete record designation */
    public function deleteRecordDesignations(Request $request) 
    {
        try {

            Designation::destroy($request->id);
            Toastr::success('Designations deleted successfully :)','Success');
            return redirect()->back();
        
        } catch(\Exception $e) {

            DB::rollback();
            Toastr::error('Designations delete fail :)','Error');
            return redirect()->back();
        }
    }
    /** page time sheet */
    public function timeSheetIndex()
    {
        return view('manager.form.timesheet');
    }

    /** page overtime */
    public function overTimeIndex()
    {
        return view('manager.form.overtime');
    }

}
