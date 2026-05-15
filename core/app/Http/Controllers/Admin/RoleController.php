<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('id','DESC')->paginate(getPaginate());
        $pageTitle = "Gestion des rôles"; 
        return view('admin.role.index', compact('pageTitle', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Ajouter un rôle";
        $permissions = Permission::get();
       
        return view('admin.role.create', compact('pageTitle','permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->get('name')]);
        $role->syncPermissions($request->get('permission'));
        $notify[] = ['success', 'Rôle ajouté avec succès'];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $pageTitle = "Rôle ".$role->name;
        $role = $role;
        $rolePermissions = $role->permissions;
        return view('admin.role.show', compact('role', 'rolePermissions','pageTitle'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $pageTitle = "Rôle ".$role->name;
        $role = $role;
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::get();
    
        return view('admin.role.edit', compact('role', 'rolePermissions', 'permissions','pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
        
        $role->update($request->only('name'));
    
        $role->syncPermissions($request->get('permission'));
    
        $notify[] = ['success', 'Rôle modifié avec succès'];
        return back()->withNotify($notify);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        $notify[] = ['success', 'Rôle supprimé avec succès'];
        return redirect()->route('admin.roles.index')->withNotify($notify);
    }
}
