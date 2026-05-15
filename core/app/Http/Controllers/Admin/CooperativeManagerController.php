<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Constants\Status;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class CooperativeManagerController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestionnaire de coopérative";
        $cooperativeManagers = User::searchable(['username', 'email', 'mobile', 'cooperative:name'])->manager()->latest('id')->with('cooperative')->paginate(getPaginate());
        return view('admin.manager.index', compact('pageTitle', 'cooperativeManagers'));
    }

    public function create()
    {
        $pageTitle = "Ajouter un gestionnaire de coopérative";
        // $cooperatives  = Cooperative::active()->orderBy('name')->get();
        $cooperatives = Cooperative::where('status', Status::YES)->orderBy('name')->get();
        $roles = Role::where('name','Manager')->latest()->get();
        return view('admin.manager.create', compact('pageTitle', 'cooperatives','roles'));
    }

    public function store(Request $request)
    {
        $validationRule = [
            'cooperative'    => 'required|exists:cooperatives,id',
            'firstname' => 'required|max:40',
            'lastname'  => 'required|max:40',
        ];

        if ($request->id) {
            $validationRule = array_merge($validationRule, [
                'email'    => 'required',
                'username' => 'required|max:40|unique:users,username,' . $request->id,
                'mobile'   => 'required',
            ]);
        } else {
            $validationRule = array_merge($validationRule, [
                'email'    => 'required',
                'username' => 'required|max:40|unique:users',
                'mobile'   => 'required',
                'password' => 'required|confirmed|min:4',

            ]);
        }

        $request->validate($validationRule);

        $cooperative = Cooperative::where('id', $request->cooperative)->first();

        if ($cooperative->status == Status::NO) {
            $notify[] = ['error', 'Cette coopérative est inactive'];
            return back()->withNotify($notify)->withInput();
        }

        if ($request->id) {
            $manager = User::findOrFail($request->id);
            $message = "Le gestionnaire a été mis à jour avec succès";
        } else {
            $manager           = new User();
            $manager->password = Hash::make($request->password);
        }

        // if($manager->cooperative_id != $request->cooperative) {
        //     $hasManager = User::manager()->where('cooperative_id', $request->cooperative)->exists();
        //     if ($hasManager) {
        //         $notify[] = ['error', 'Cette coopérative a déjà un gestionnaire'];
        //         return back()->withNotify($notify)->withInput();
        //     }
        // }


        $manager->cooperative_id = $request->cooperative;
        $manager->firstname = $request->firstname;
        $manager->lastname  = $request->lastname;
        $manager->username  = $request->username;
        $manager->email     = $request->email;
        $manager->mobile    = $request->mobile;
        $manager->password  = $request->password ? Hash::make($request->password) : $manager->password;
        $manager->user_type = "manager";
        $manager->type_compte = "mobile-web";
        $manager->save();

        if (!$request->id) {
            $manager->syncRoles($request->role);
            notify($manager, 'MANAGER_CREATE', [
                'username' => $manager->username,
                'email'    => $manager->email,
                'password' => $request->password,
            ]);
        }else{
            
            DB::table('model_has_roles')->where('model_id', $request->id)->delete();
           $manager->syncRoles($request->get('role'));
        }

        $notify[] = ['success', isset($message) ? $message : 'Le gestionnaire a été ajouté avec succès'];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = "Mise à jour du gestionnaire de coopérative"; 
        $manager   = User::findOrFail($id);
        $cooperatives = Cooperative::where('status', Status::YES)->orderBy('name')->get();
        $userRole = $manager->roles->pluck('name')->toArray(); 
        $roles = Role::latest()->get();
        return view('admin.manager.edit', compact('pageTitle', 'cooperatives', 'manager','userRole', 'roles'));
    }

    public function staffList($cooperativeId)
    {
        $pageTitle = "Liste de Staffs";
        $staffs = User::searchable(['username', 'email', 'mobile', 'cooperative:name'])->staff()->where('cooperative_id', $cooperativeId)->with('cooperative')->paginate(getPaginate());
        return view('admin.manager.staff', compact('pageTitle', 'staffs'));
    }

    public function status($id)
    {
        return User::changeStatus($id);
    }

    public function login($id)
    {  
        
       $user = User::manager()->where('id', $id)->firstOrFail();
    
       if(cooperative() !=null && cooperative()->id != $user->cooperative_id){ 
        session(['cooperative' => []]);
       }
    
        auth()->loginUsingId($id);
        
      return to_route('manager.dashboard');
    }

    public function staffLogin($id)
    {
        $user = User::staff()->where('id', $id)->firstOrFail();
        auth()->loginUsingId($user->id);
        return to_route('staff.dashboard');
    }

    public function cooperativeManager($id)
    {
        $cooperative         = Cooperative::findOrFail($id);
        $pageTitle      = $cooperative->name . "Liste des Managers";
        $cooperativeManagers = User::manager()->where('cooperative_id', $id)->orderBy('id', 'DESC')->with('cooperative')->paginate(getPaginate());
        return view('admin.manager.index', compact('pageTitle', 'cooperativeManagers'));
    }
}
