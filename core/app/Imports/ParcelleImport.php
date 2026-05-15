<?php

namespace App\Imports;

use App\Models\Parcelle;
use App\Models\Producteur;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithValidation;

class ParcelleImport implements ToCollection, WithHeadingRow, WithValidation
{
  /**
   * @param Collection $collection
   */
  public function rules(): array
  {
    return [
      'superficie' => 'required',
      'codeproducteur' => 'required',
    ];
  }
  public function collection(Collection $collection)
  {

    $j = 0;
    $k = '';
    if (count($collection)) {

      foreach ($collection as $row) {

        $codeProd = $row['codeproducteur']; //Get the user emails
        $verification = DB::table('producteurs')->orWhere('codeProd', $codeProd)->orWhere('codeProdapp', $codeProd)->first();
        if ($verification != null) {
          if ($row['codeparcelle']) {
            $codeParc = $row['codeparcelle'];
          } else {
            $codeProd = $verification->codeProdapp;
            $codeParc = $this->generecodeparc($verification->id, $codeProd);
          }

          $superficie = $row['superficie'];
          $superficie = Str::before($superficie, ' ');
          if (Str::contains($superficie, ",")) {
            $superficie = Str::replaceFirst(',', '.', $superficie);
            if (Str::contains($superficie, ",")) {
              $superficie = Str::replaceFirst('m²', '', $superficie);
            }
          }

          $insert_data = array(
            'producteur_id' => $verification->id,
            'codeParc' => $codeParc,
            'anneeCreation' => $row['anneecreation'],
            'typedeclaration' => 'Verbale',
            'culture' => $row['cultureparcelle'],
            'superficie' => is_numeric(trim($superficie)) ? round(trim($superficie), 2) : trim($superficie),
            'latitude' => round($row['latitude'], 6),
            'longitude' => round($row['longitude'], 6),
            'userid' => auth()->user()->id,
            'created_at' => NOW(),
            'updated_at' => NOW()
          );
          DB::table('parcelles')->insert($insert_data);
          $j++;
        } else {
          $k .= $codeProd . ' , ';
          $notify[] = ['error', "Les Producteurs dont les codes suivent : $k n'existent pas dans la base."];
          return back()->withNotify($notify);
        }
      }

      if (!empty($j)) {
        $notify[] = ['success', "$j Parcelles ont été crée avec succès"];
        return back()->withNotify($notify);
        if ($k != '') {
          $notify[] = ['error', "Les Producteurs dont les codes suivent : $k n'existent pas dans la base."];
          return back()->withNotify($notify);
        }
      } else {
        if ($k != '') {

          $notify[] = ['error', "Les Producteurs dont les codes suivent : $k n'existent pas dans la base."];
          return back()->withNotify($notify);
        }
      }
    } else {

      $notify[] = ['error', "Il n'y a aucune données dans le fichier"];
      return back()->withNotify($notify);
    }
  }


  private function generecodeparc($idProd, $codeProd)
  {
    if ($codeProd) {
      $action = 'non';

      $data = Parcelle::select('codeParc')->where([
        ['producteur_id', $idProd],
        ['codeParc', '!=', null]
      ])->orderby('id', 'desc')->first();

      if ($data != '') {

        $code = $data->codeParc;

        if ($code != '') {
          $chaine_number = Str::afterLast($code, '-');
          $numero = Str::after($chaine_number, 'P');
          $numero = $numero + 1;
        } else {
          $numero = 1;
        }
        $codeParc = $codeProd . '-P' . $numero;

        do {

          $verif = Parcelle::select('codeParc')->where('codeParc', $codeParc)->orderby('id', 'desc')->first();
          if ($verif == null) {
            $action = 'non';
          } else {
            $action = 'oui';
            $code = $data->codeParc;

            if ($code != '') {
              $chaine_number = Str::afterLast($code, '-');
              $numero = Str::after($chaine_number, 'P');
              $numero = $numero + 1;
            } else {
              $numero = 1;
            }
            $codeParc = $codeProd . '-P' . $numero;
          }
        } while ($action != 'non');
      } else {
        $codeParc = $codeProd . '-P1';
      }
    } else {
      $codeParc = '';
    }

    return $codeParc;
  }
}
