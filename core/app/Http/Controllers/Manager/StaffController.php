<?php

namespace App\Http\Controllers\Manager;

use Exception;
use App\Models\User;
use App\Models\Localite;
use App\Models\Cooperative;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ExportStaffs;
use App\Models\User_localite;
use App\Models\EmployeeDetail;
use App\Models\MagasinSection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function create()
    {
        $pageTitle = "Ajouter un Staff";
        //recuperation des roles
        $roles = Role::latest()->get();
        //fin recuperation
        $manager   = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });

        return view('manager.staff.create', compact('pageTitle', 'localites', 'roles', 'sections'));
    }

    public function index()
    {
        $pageTitle = __("Tous les Staff");
        $manager   = auth()->user();
        $staffs    = User::searchable(['username','firstname','lastname','email'])->where(function ($query) use ($manager) {
            $query->staff()->where('cooperative_id', $manager->cooperative_id);
        })->with('cooperative')->orderBy('id', 'DESC')->paginate(getPaginate());

        return view('manager.staff.index', compact('pageTitle', 'staffs'));
    }
    public function magasinIndex($staffId)
    {
        $pageTitle = "Tous les Magasins";
        $manager   = auth()->user();
        $magasins    = MagasinSection::where('staff_id', $staffId)->with('user')->orderBy('id', 'DESC')->paginate(getPaginate());

        return view('manager.staff.magasin', compact('pageTitle', 'magasins', 'staffId'));
    }

    public function edit($id, User $user)
    {
        try {
            $id = decrypt($id);
        } catch (Exception $ex) {
            $notify[] = ['error', "Invalid URL."];
            return back()->withNotify($id);
        }

        $pageTitle = "Mise à jour du Staff";
        $manager   = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $staff     = User::where('id', $id)->where('cooperative_id', $manager->cooperative_id)->firstOrFail();
        //ajout des roles et le role de l'étulisateur dans la vue manager.staff.edit
        $userRole = $staff->roles->pluck('name')->toArray();
        $roles = Role::latest()->get();
        //fin
        $userLocalite = array();
        $userSection = array();
        $dataLocalite = $staff->userLocalites;
        
        if ($dataLocalite->count()) {
            
            foreach ($dataLocalite as $data) {
                $userLocalite[] = @$data->localite_id;
            }
            foreach ($dataLocalite as $data) {
               
                $userSection[] = @$data->localite->section->id;
            }
        }
        
        return view('manager.staff.edit', compact('pageTitle', 'staff', 'localites', 'userLocalite', 'userRole', 'roles', 'sections', 'userSection'));
    }
public function getLocalite(Request $request){
 
    $localites = Localite::whereIn('section_id', $request->section)->get();
   
    if ($localites->count()) {
       
        $contents = '';

        foreach ($localites as $data) {
            $contents .= '<option value="' . $data->id . '" data-chained="'.$data->section_id.'">'. $data->nom . '</option>';
        }
         
    } else {
        $contents = null;
    }
return $contents;
}
    public function store(Request $request)
    {
        $manager        = auth()->user();
        $validationRule = [
            'firstname' => 'required|max:40',
            'lastname'  => 'required|max:40',
        ];

        if ($request->id) {
            $validationRule = array_merge($validationRule, [
                'username' => 'required|max:40|unique:users,username,' . $request->id, 
                'password' => 'nullable|confirmed|min:4',
                'role'   => 'required|max:40',
                'type_compte'   => 'required|max:40',
            ]);
        } else {
            $validationRule = array_merge($validationRule, [
                'username' => 'required|max:40|unique:users', 
                'password' => 'required|confirmed|min:4',
                'role'   => 'required|max:40',
                'type_compte'   => 'required|max:40',
            ]);
        }

        $request->validate($validationRule);

        $staff = new User();


        if ($request->id) {
            $staff   = User::where('id', $request->id)->where('cooperative_id', $manager->cooperative_id)->firstOrFail();
            $message = "Staff updated successfully";
        }
        if (($request->type_compte == 'web') ||  ($request->type_compte == 'mobile-web')) {
            $hasStaff = User::where([['cooperative_id', auth()->user()->cooperative_id],['user_type','!=','manager']])->where(function ($query) {
                $query->orwhere('type_compte', 'web');
                $query->orwhere('type_compte', 'mobile-web');
            })->count();
            
            if ($hasStaff >= auth()->user()->cooperative->web) {
                $nombre = auth()->user()->cooperative->web;
                $notify[] = ['error', "Cette coopérative a atteint le nombre de compte Web qui est de : $nombre utilisateurs"];
                return back()->withNotify($notify)->withInput();
            }
        }
        if (($request->type_compte == 'mobile') ||  ($request->type_compte == 'mobile-web')) {
            $hasStaff = User::where([['cooperative_id', auth()->user()->cooperative_id],['user_type','!=','manager']])->where(function ($query) {
                $query->orwhere('type_compte', 'mobile');
                $query->orwhere('type_compte', 'mobile-web');
            })->count();
            if ($hasStaff >= auth()->user()->cooperative->mobile) {
                $nombre = auth()->user()->cooperative->mobile;
                $notify[] = ['error', "Cette coopérative a atteint le nombre de compte Mobile qui est de : $nombre utilisateurs"];
                return back()->withNotify($notify)->withInput();
            }
        }

        $staff->cooperative_id = $manager->cooperative_id;
        $staff->firstname = trim($request->firstname);
        $staff->lastname  = trim($request->lastname);
        $staff->username  = trim($request->username);
        $staff->email     = trim($request->email);
        $staff->mobile    = trim($request->mobile);
        $staff->adresse    = trim($request->adresse);
        $staff->user_type = 'staff';
        $staff->type_compte = $request->type_compte;
        $staff->password  = $request->password ? Hash::make($request->password) : $staff->password;
        //$staff->syncRoles($request->get('rolePermission'));
        $staff->save();

        if (!$request->id) {

            if($staff->id) {
                $lastEmployeeID = EmployeeDetail::where('cooperative_id', auth()->user()->cooperative_id)->count();
                 if($lastEmployeeID){
                        $lastEmployeeID = $lastEmployeeID+1;
                    $employeeid = 'EMP-'.$lastEmployeeID;
                    }else{
                        $employeeid ="EMP-1";
                    }
                $employee = new EmployeeDetail();
                $employee->user_id = $staff->id; 
                $employee->employee_id =  $employeeid;
                $employee->cooperative_id = $staff->cooperative_id; 
                $employee->save(); 
            }
            $staff->syncRoles($request->role);

            $id = $staff->id;

            if (($request->localite != null)) {

                $verification   = User_localite::where('user_id', $id)->get();
                if ($verification->count()) {
                    DB::table('user_localites')->where('user_id', $id)->delete();
                }
                $i = 0;

                foreach ($request->localite as $data) {
                    if ($data != null) {
                        DB::table('user_localites')->insert(['user_id' => $id, 'localite_id' => $data]);
                    }
                    $i++;
                }
            }
        } else {
            DB::table('model_has_roles')->where('model_id', $request->id)->delete();
            $staff->syncRoles($request->get('role'));
            if (($request->localite != null)) {

                $verification   = User_localite::where('user_id', $request->id)->get();
                if ($verification->count()) {
                    DB::table('user_localites')->where('user_id', $request->id)->delete();
                }
                $i = 0;

                foreach ($request->localite as $data) {
                    if ($data != null) {
                        DB::table('user_localites')->insert(['user_id' => $request->id, 'localite_id' => $data]);
                    }
                    $i++;
                }
            }

        }

        if (!$request->id) {
            notify($staff, 'STAFF_CREATE', [
                'username' => $staff->username,
                'email'    => $staff->email,
                'password' => $request->password,
            ]);
        }

        $notify[] = ['success', isset($message) ? $message : 'Staff added successfully'];
        return back()->withNotify($notify);
    }
    public function staffLogin($id)
    {
        User::staff()->where('id', $id)->firstOrFail();
        auth()->loginUsingId($id);
        // return to_route('staff.dashboard');
        return to_route('manager.dashboard');
    }

    public function magasinStore(Request $request)
    {
        $request->validate([
            'nom'  => 'required',
            'staff'  => 'required',
            'phone'  => 'required',
        ]);


        if ($request->id) {
            $magasin    = MagasinSection::findOrFail($request->id);
            $message = "Magasin a été mise à jour avec succès.";
        } else {
            $magasin = new MagasinSection();
            $magasin->code = $this->generecodemagasin();
        }

        $magasin->nom    = $request->nom;
        $magasin->staff_id = $request->staff;
        $magasin->phone = $request->phone;
        $magasin->email = $request->email;
        $magasin->adresse   = $request->adresse;
        $magasin->save();
        $notify[] = ['success', isset($message) ? $message  : 'Le magasin a été ajouté avec succès.'];
        return back()->withNotify($notify);
    }
    private function generecodemagasin()
    {

        $data = MagasinSection::select('code')->orderby('id', 'desc')->first();

        if ($data != '') {
            $code = $data->code;
            $chaine_number = Str::afterLast($code, '-');
            if ($chaine_number < 10) {
                $zero = "00000";
            } else if ($chaine_number < 100) {
                $zero = "0000";
            } else if ($chaine_number < 1000) {
                $zero = "000";
            } else if ($chaine_number < 10000) {
                $zero = "00";
            } else if ($chaine_number < 100000) {
                $zero = "0";
            } else {
                $zero = "";
            }
        } else {
            $zero = "00000";
            $chaine_number = 0;
        }
        $sub = 'MAG-';
        $lastCode = $chaine_number + 1;
        $codeMagasinsections = $sub . $zero . $lastCode;

        return $codeMagasinsections;
    }
    public function status($id)
    {
        return User::changeStatus($id);
    }
    public function magasinStatus($id)
    {
        return MagasinSection::changeStatus($id);
    }
    public function exportExcel()
    {
        return (new ExportStaffs())->download('staffs.xlsx');
    }

    public function delete($id)
    { 
        User::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return redirect()->route('manager.staff.index')->withNotify($notify);
    }

}
