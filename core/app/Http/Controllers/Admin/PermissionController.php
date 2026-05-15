<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Exception;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle = "Gestion des permissions"; 
        $permissions = Permission::orderBy('id','DESC')->paginate(getPaginate());
        return view('admin.permission.index',compact('pageTitle', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Ajouter une permission";    
        return view('admin.permission.create',compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRule = [
            'name' => 'required|unique:permissions,name',
        ];
        $request->validate($validationRule);
        Permission::create($request->only('name'));
        $notify[] = ['success', 'Permission ajouté avec succès'];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      
        $pageTitle = "Mise à jour de la permission";
        $permission = Permission::where('id',$id)->firstOrFail();
        return view('admin.permission.edit', compact('pageTitle', 'permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
       
        $validationRule = [
            'name' => 'required|unique:permissions,name,'.$permission->id,
        ];
        
        $request->validate($validationRule);
        $permission->update($request->only('name'));
        $notify[] = ['success', 'Permission mise à jour avec succès'];
        return back()->withNotify($notify);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
