<?php
namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\Cooperative;
use App\Models\Localite;
use App\Models\Producteur; 
use App\Models\Archivage;
use App\Models\TypeArchive;
use Maatwebsite\Excel\Facades\Excel; 
use Illuminate\Support\Facades\File;
use Mpdf\Mpdf;
use Mpdf\Output\Destination; 
use Illuminate\Support\Facades\DB;
 

class ArchivageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
	
        $data=array();
        $manager   = auth()->user();
        
 
        $activePage ='archivages';
        $pageTitle = 'Gestion des Archives';
        $typearchives = TypeArchive::get();
        $archivages = Archivage::dateFilter()
            ->searchable(["titre", "resume", "document"])
            ->latest('id') 
            ->where(function ($q) {
                if (request()->type_archive != null) {
                    $q->where('type_archive_id', request()->type_archive);
                } 
            })
            ->with('cooperative','typeArchive')
            ->where('archivages.cooperative_id', $manager->cooperative_id)
            ->paginate(getPaginate());
 
        return view('manager.archivages.index', compact('activePage','pageTitle','typearchives','archivages'));
    }

 
    public function export()
    {
      $filename='suivi-parcelles-'.gmdate('dmYhms').'.xlsx';

     return Excel::download(new SuiviParcelleExport, $filename);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	
        $data=array(); 
        
            $data['type_archives'] = DB::table('type_archives')->pluck('nom','id')->all();
        $data['activePage'] ='archivages';
        $data['pageTitle'] = "Création d'une archive";
        return view('manager.archivages.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $manager   = auth()->user();
        $this->validate($request,[
            'document' => 'mimes:doc,docx,xlsx,xls,pdf,ppt,pptx|max:4048',
           ]);

           if(!file_exists(storage_path(). "/app/public/archivages")){ 
            File::makeDirectory(storage_path(). "/app/public/archivages", 0777, true);
          }

        $titre = Str::slug($request->titre,'-');
        if($request->document){
          $fileName = $titre.'.'.$request->document->extension();
          $document = $request->file('document')->move(storage_path(). "/app/public/archivages",$fileName);

          $document = "archivages/$fileName";
        }

        if(isset($request->content)){
           //create PDF
        $mpdf = new Mpdf();
 
        //write content
        $mpdf->WriteHTML($request->get('content'));

        //return the PDF for download
        $document = "archivages/$titre.pdf";
        $location = storage_path(). "/app/public/archivages/".$titre.'.pdf';
       $mpdf->Output($location, Destination::FILE);
        }
$archive = new Archivage();
$archive->cooperative_id = $manager->cooperative_id;
$archive->type_archive_id = $request->type_archive_id;
$archive->titre = $request->titre;
$archive->resume = $request->resume;
$archive->document = $document;
$archive->userid = $manager->id;
$archive->save(); 
$notify[] = ['success', 'Le fichier d\'archivages a été crée avec succès.'];
         return redirect()->route('manager.archivages.index')->withNotify($notify);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
	
      $data=array();
      $data['archivages'] = Archivage::select('archivages.*','p.nom as nomProd','p.prenoms','p.codeProdapp','l.nom as nomLocal','l.codeLocal','c.nom as nomCoop','c.codeCoop')
                ->join('parcelles as pa','archivages.parcelles_id','=','pa.id')
                ->join('producteurs as p','pa.producteurs_id','=','p.id')
                ->join('localites as l','p.localites_id','=','l.id')
                ->join('cooperatives as c','l.cooperatives_id','=','c.id')
                ->find($id);
         $data['pageTitle'] = 'Details Archive';
      return view('archivages.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
	
        $data=array();
        $data['archivages'] = Archivage::find($id); 
 
            $data['type_archives'] = DB::table('type_archives')->pluck('nom','id')->all(); 

            $data['activePage'] ='archivages';
            $data['pageTitle'] = 'Modification Archive';
        return view('manager.archivages.edit', $data);
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $manager   = auth()->user();
        $this->validate($request,[
            'document' => 'mimes:doc,docx,xlsx,xls,pdf,ppt,pptx|max:4048',
           ]);
           if(!file_exists(storage_path(). "/app/public/archivages")){ 
            File::makeDirectory(storage_path(). "/app/public/archivages", 0777, true);
          }
        $archive = Archivage::find($id);

        // if($request->document){
        //     $document = $request->file('document')->store('archivages');
        //     $document = $document;
        //   }
        $titre = Str::slug($request->titre,'-');
        if($request->document){
          $fileName = $titre.'.'.$request->document->extension();
          $document = $request->file('document')->move(storage_path(). "/app/public/archivages",$fileName);
          //$location = storage_path(). "/app/public/archivages/".$titre.'.pdf';
          $document = "archivages/$fileName";
        }
        $archive->cooperative_id = $manager->cooperative_id;
        $archive->type_archive_id = $request->type_archive_id;
        $archive->titre = $request->titre;
        $archive->resume = $request->resume;
        $archive->document = $request->document ? $document : $request->old_document;
        $archive->userid = $manager->id;
        $archive->save(); 
        $notify[] = ['success', 'Le fichier d\'archivages a été mise à jour avec succès.'];
         return redirect()->route('manager.archivages.index')->withNotify($notify);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
	
      Archivage::find($id)->delete();

        return redirect()->route('archivages.index')
                        ->with('success','Le fichier d\'archivages a été supprimée avec succès.');
    }

    public function destroyFinally($id)
    {
	

        DB::table("archivages")->where('id',$id)->delete();
        return redirect()->back()->with('success','Archivage a été supprimé définitivement avec succès.');
    }
    public function restore($id)
    {
	
      Archivage::withTrashed()->find($id)->restore();

        return redirect()->back();
    }

    public function restoreAll()
    {
	
      Archivage::onlyTrashed()->restore();

        return redirect()->back();
    }
    public function status($id)
    {
        return Archivage::changeStatus($id);
    }
}
