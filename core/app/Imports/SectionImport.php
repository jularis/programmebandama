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

class SectionImport implements ToCollection, WithHeadingRow, WithValidation
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
        $j = 0;
        $k = '';
        if (count($collection)) {

            foreach ($collection as $row) {

                $nom = $this->verifysection($row['nom']);

                $insert_data = array(
                    'cooperative_id' => auth()->user()->cooperative_id, 
                    'libelle' => $nom,
                    'sousPrefecture' => 'N/A',
                    'created_at' => NOW(),
                    'updated_at' => NOW()
                );

                DB::table('sections')->insert($insert_data);
                $j++;
            }

            if (!empty($j)) {
                $notify[] = ['success', "$j Section(s) ont été crée avec succès."];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "Aucune Section n'a été ajouté à la base car ils existent déjà."];
                return back()->withNotify($notify);
            }
        } else {

            $notify[] = ['error', "Il n'y a aucune données dans le fichier."];
            return back()->withNotify($notify);
        }
    }


    private function verifysection($nom)
    {
        $action = 'non';
        do {
            $data = Section::select('libelle')->where([['cooperative_id',auth()->user()->cooperative_id],['libelle', $nom]])->orderby('id', 'desc')->first();
            if ($data != '') {

                $nomSection = $data->libelle;
                $nom = Str::beforeLast($nomSection, ' ');
                $chaine_number = Str::afterLast($nomSection, ' ');

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
                $nomSection = $sub . $zero . $lastCode;
            } else {

                $nomSection = $nom;
            }
            $verif = Section::select('libelle')->where([['cooperative_id',auth()->user()->cooperative_id],['libelle', $nomSection]])->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $nom = $verif->libelle;
            }
        } while ($action != 'non');

        return $nomSection;
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
