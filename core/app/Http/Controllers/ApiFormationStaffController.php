<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Campagne;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\FormationStaff;
use Illuminate\Routing\Controller;
use App\Models\FormationStaffListe;
use Illuminate\Support\Facades\File;
use App\Models\FormationStaffVisiteur;
use App\Models\SuiviFormationVisiteur;
use App\Models\FormationStaffFormateur;
use App\Models\FormationStaffModuleTheme;

class ApiFormationStaffController extends Controller
{
    public function store(Request $request)
    {
		 
        if(!file_exists(storage_path(). "/app/public/formationsStaff"))
        { 
            File::makeDirectory(storage_path(). "/app/public/formationsStaff", 0777, true);
        }
       
        
        if ($request->id) {
            $formation = FormationStaff::findOrFail($request->id);
        } else {
            $formation = new FormationStaff();
        }
        $campagne = Campagne::active()->first();
        $manager = User::where('id', $request->userid)->first();
        $formation->cooperative_id  = $manager->cooperative_id;
        $formation->campagne_id  = $campagne->id;
        $formation->lieu_formation  = $request->lieu_formation;
        $formation->formateur_staff_id  = $request->formateur;
        $formation->observation_formation = $request->observation_formation;
        $formation->duree_formation     = $request->duree_formation;

        $formation->date_debut_formation = $request->multiStartDate;
        $formation->date_fin_formation = $request->multiEndDate;

        $photo_fileNameExtension = Str::afterLast($request->photo_filename, '.');
        $rapport_fileNameExtension = Str::afterLast($request->rapport_filename,'.');

        if ($request->photo_formation) {
            $image = $request->	photo_formation;
            $image = Str::after($image, 'base64,');
            $image = str_replace(' ', '+', $image);
            $imageName = (string) Str::uuid() . '.' . $photo_fileNameExtension;
            File::put(storage_path() . "/app/public/formationsStaff" . $imageName, base64_decode($image));
            $photo_formations = "public/formationsStaff/$imageName";
            $formation->photo_formation = $photo_formations;
        }

        if($request->rapport_formation){
            $rapport_formation = $request->rapport_formation;
            $rapport_formation = Str::after($rapport_formation, 'base64,');
            $rapport_formation = str_replace(' ', '+', $rapport_formation);
            $rapportName = (string) Str::uuid() . '.' . $rapport_fileNameExtension;
            File::put(storage_path() . "public/formationsStaff/" . $rapportName, base64_decode($rapport_formation));
            $rapport_formation = "public/formationsStaffs/$rapportName";

            $formation->rapport_formation = $rapport_formation;
        }
        $formation->save();
        if ($formation != null) {
            $id = $formation->id;
            $datas = $datas2 = $datas3 = $datas4 = [];
            if (($request->user != null)) {
                FormationStaffListe::where('formation_staff_id', $id)->delete();
                $i = 0;
                foreach ($request->user as $data) {
                    if ($data != null) {
                        $datas[] = [
                            'formation_staff_id' => $id,
                            'user_id' => $data,
                        ];
                    }
                    $i++;
                }
            }
            if (($request->visiteurs != null)) {
                FormationStaffVisiteur::where('formation_staff_id', $id)->delete();
                $i = 0;
                foreach ($request->visiteurs as $data) {
                    if ($data != null) {
                        $datas2[] = [
                            'formation_staff_id' => $id,
                            'visiteur' => $data,
                        ];
                    }
                    $i++;
                }
            }
  
            $selectedThemes = $request->theme;
            $selectedModules = $request->module_formation;

            if ($selectedThemes != null && $selectedModules != null) {
                FormationStaffModuleTheme::where('formation_staff_id', $id)->delete();
               
                foreach ($selectedThemes as $themeId) {
                    list($moduleFormationId, $themeItemId) = explode('-', $themeId);
                    $datas3[] = [
                        'formation_staff_id' => $id,
                        'module_formation_staff_id' => $moduleFormationId,
                        'theme_formation_staff_id' => $themeItemId,
                    ];
                }
                FormationStaffModuleTheme::insert($datas3);
            }
            if (($request->formateur != null)) {
                FormationStaffFormateur::where('formation_staff_id', $id)->delete();
                $datas4[] = [
                    'formation_staff_id' => $id,
                    'formateur_staff_id' => $request->formateur,
                ];
            }
            FormationStaffListe::insert($datas);
            FormationStaffVisiteur::insert($datas2);
            FormationStaffFormateur::insert($datas4);
        }
        return response()->json($formation, 201);
    }

    
}
