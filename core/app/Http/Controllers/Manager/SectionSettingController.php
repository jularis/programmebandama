<?php

namespace App\Http\Controllers\Manager;
use App\Models\Section;
use App\Models\Localite;
use App\Constants\Status;
use App\Http\Helpers\Reply;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use App\Imports\SectionImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SectionSettingController extends Controller
{
    public function index()
    {
        $pageTitle = "Gestion des sections"; 
        $manager   = auth()->user();
        $cooperatives = Cooperative::where('status', Status::YES)->where('id',$manager->cooperative_id)->orderBy('name')->get();
        // $sections = Section::orderBy('created_at','desc')->with('cooperative')->paginate(getPaginate());
        $activeSettingMenu = 'section_settings';
        $sections = Section::latest('id')->joinRelationship('cooperative')->where('cooperative_id',$manager->cooperative_id)->with('cooperative')->paginate(getPaginate());
        
        return view('manager.section-settings.index',compact('pageTitle','cooperatives','sections','activeSettingMenu'));
    }

    public function create()
    {
        $pageTitle = "Ajouter une section";
        $manager   = auth()->user();
        $activeSettingMenu = 'section_settings';
        $cooperatives = Cooperative::where('status', Status::YES)->where('id',$manager->cooperative_id)->orderBy('name')->get();
        return view('manager.section-settings.create', compact('pageTitle','cooperatives','activeSettingMenu'));
    }

    public function store(StoreSectionRequest $request){
        $valitedData = $request->validated();

        Section::create($valitedData);

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('manager.settings.section-settings.index')]);
    }
    public function edit($id)
    {
        $pageTitle = "Modifier une section";
        $activeSettingMenu = 'section_settings';
        try {
            $section = Section::findOrFail($id);
            $manager   = auth()->user();
            $cooperatives  = Cooperative::where('id',$manager->cooperative_id)->orderBy('name')->get();
            
            return view('manager.section-settings.edit', compact('pageTitle','section','cooperatives','activeSettingMenu'));
        } catch (ModelNotFoundException $e) {
            // L'enregistrement n'a pas été trouvé, vous pouvez rediriger ou afficher un message d'erreur
            return redirect()->route('manager.settings.section-settings.index')->with('error', 'La section demandée n\'existe pas.','activeSettingMenu');
        }
      
    }

    public function update(UpdateSectionRequest $request, $id)
    {
        $valitedData = $request->validated();
        $section = Section::findOrFail($id);
        $section->update($valitedData);
        
        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('manager.settings.section-settings.index')]);
    }
    //lister les localités d'une section
    public function localiteSection($id)
    {
        $pageTitle = "Gestion des localités de la section ". Section::find($id)->libelle;
        $cooperativeLocalites = Localite::active()->where('section_id',$id)->with('section.cooperative')->paginate(getPaginate());
        return view('manager.localite.index',compact('cooperativeLocalites','pageTitle'));
    }
    //traitement pour enregistrer une localité d'une section
    public function storelocalitesection(){

    }

    //traitement pour modifier une localité d'une section
    public function updatelocalitesection($id){

    }
    //affichant le formulaire de modification d'une localité d'une section
    public function localitesectionedit($id){

    }
    public function  uploadContent(Request $request)
    {
        Excel::import(new SectionImport, $request->file('uploaded_file'));
        return back();
    }
    public function destroy($id)
    {
        Section::destroy($id);
        return Reply::success(__('messages.deleteSuccess'));
    }

}
