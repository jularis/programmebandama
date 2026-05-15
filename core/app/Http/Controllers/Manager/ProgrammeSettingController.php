<?php

namespace App\Http\Controllers\Manager;
use App\Http\Helpers\Reply;
use App\Models\Programme;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgrammeDurabilite;
use App\Http\Requests\UpdateProgrammeDurabilite;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProgrammeSettingController extends Controller
{
    public function index()
    {
        $pageTitle = "Gestion des programmes de durabilité";
        $programmeDurabilites = Programme::orderBy('created_at','desc')->paginate(getPaginate());
        $activeSettingMenu = 'durabilite_settings';
        return view('manager.programme-settings.index',compact('pageTitle','programmeDurabilites','activeSettingMenu'));
    }

    public function create()
    {
        $pageTitle = "Ajouter un programme de durabilité";
        $activeSettingMenu = 'durabilite_settings';
        return view('manager.programme-settings.create',compact('pageTitle','activeSettingMenu'));
    }

    public function store(StoreProgrammeDurabilite $request)
    {
        $valitedData = $request->validated();
            
        Programme::create($valitedData);
     
        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('manager.settings.durabilite-settings.index')]);
    }

    public function edit($id)
    {
        $pageTitle = "Modifier un programme de durabilité";
        $activeSettingMenu = 'durabilite_settings';
        try { 
            $programme = Programme::findOrFail($id);
            return view('manager.programme-settings.edit', compact('pageTitle','programme','activeSettingMenu'));
        } catch (ModelNotFoundException $e) {
            // L'enregistrement n'a pas été trouvé, vous pouvez rediriger ou afficher un message d'erreur
            return redirect()->route('manager.settings.durabilite-settings.index')->with('error', 'La section demandée n\'existe pas.');
        }
    }
    public function update(UpdateProgrammeDurabilite $request, $id)
    {
        $valitedData = $request->validated();
        try {
            $programme = Programme::findOrFail($id);
            $programme->update($valitedData); 
            return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('manager.settings.durabilite-settings.index')]);
        } catch (ModelNotFoundException $e) {
            // L'enregistrement n'a pas été trouvé, vous pouvez rediriger ou afficher un message d'erreur
            
            return Reply::success(__('messages.updateError'));
        }
    }
    public function destroy($id)
    {
        Programme::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
