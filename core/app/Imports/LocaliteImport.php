<?php

namespace App\Imports;

use App\Models\Localite;
use App\Models\Producteur;
use App\Models\Section;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithValidation;

class LocaliteImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
    * @param Collection $collection
    */
    public function rules(): array
    {
        return [
            'nom' => 'required',
        ];
    }
    public function collection(Collection $collection)
    {
        $section = request()->section;
        $j = 0;
        $k = '';
        if (count($collection)) {

            foreach ($collection as $row) {

                $nom = $this->verifylocalite($row['nom']);
                $codeLocal = $this->generelocalitecode($nom);
                if($row['section'] !=null){
                    $section = Section::where([['libelle',$row['section']],['cooperative_id',auth()->user()->cooperative_id]])->first();
                    $section = $section->id;
                }

                $insert_data = array(
                    'section_id' => $section,
                    'codeLocal' => $codeLocal,
                    'nom' => $nom,
                    'type_localites' => 'N/A',
                    'sousprefecture' => 'N/A',
                    'centresante' => 'N/A',
                    'typecentre' => 'N/A',
                    'ecole' => 'N/A',
                    'electricite' => 'N/A',
                    'userid' => auth('admin')->check() ? auth('admin')->user()->id : auth()->user()->id,
                    'created_at' => NOW(),
                    'updated_at' => NOW()
                );

                DB::table('localites')->insert($insert_data);
                $j++;
            }

            if (!empty($j)) {
                $notify[] = ['success', "$j Localités ont été crée avec succès."];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "Aucune Localité n'a été ajouté à la base car ils existent déjà."];
                return back()->withNotify($notify);
            }
        } else {

            $notify[] = ['error', "Il n'y a aucune données dans le fichier."];
            return back()->withNotify($notify);
        }
    }


    private function verifylocalite($nom)
    {
        $action = 'non';
        do {
            $data = Localite::select('nom')->where('nom', $nom)->orderby('id', 'desc')->first();
            if ($data != '') {

                $nomLocal = $data->nom;
                $nom = Str::beforeLast($nomLocal, ' ');
                $chaine_number = Str::afterLast($nomLocal, ' ');

                if (is_numeric($chaine_number) && ($chaine_number < 10)) {
                    $zero = "00";
                } else if (is_numeric($chaine_number) && ($chaine_number < 100)) {
                    $zero = "0";
                } else {
                    $zero = "00";
                    $chaine_number = 0;
                }

                $sub = $nom . ' ';
                $lastCode = $chaine_number + 1;
                $nomLocal = $sub . $zero . $lastCode;
            } else {

                $nomLocal = $nom;
            }
            $verif = Localite::select('nom')->where('nom', $nomLocal)->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $nom = $verif->nom;
            }
        } while ($action != 'non');

        return $nomLocal;
    }
    private function generelocalitecode($name)
    {
        $action = 'non';
        do {

            $data = Localite::select('codeLocal')->where('nom', $name)->orderby('id', 'desc')->first();

            if ($data != '') {

                $code = $data->codeLocal;

                $chaine_number = Str::afterLast($code, '-');

                if ($chaine_number < 10) {
                    $zero = "00";
                } else if ($chaine_number < 100) {
                    $zero = "0";
                } else {
                    $zero = "";
                }
            } else {
                $zero = "00";
                $chaine_number = 0;
            }

            $abrege = Str::upper(Str::substr($name, 0, 3));
            $sub = $abrege . '-';
            $lastCode = $chaine_number + 1;
            $codeP = $sub . $zero . $lastCode;

            $verif = Localite::select('nom')->where('codeLocal', $codeP)->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $name = $verif->nom;
            }
        } while ($action != 'non');

        return $codeP;
    }
}
