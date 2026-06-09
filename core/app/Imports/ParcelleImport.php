<?php

namespace App\Imports;

use App\Models\Parcelle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ParcelleImport implements ToCollection, WithHeadingRow, WithValidation
{
  public int $created = 0;
  public int $updated = 0;
  public array $missingProducteurs = [];

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
    if (!count($collection)) {
      return;
    }

    foreach ($collection as $row) {
      $codeProd = trim((string) $row['codeproducteur']);
      $verification = DB::table('producteurs')
        ->orWhere('codeProd', $codeProd)
        ->orWhere('codeProdapp', $codeProd)
        ->first();

      if ($verification == null) {
        $this->missingProducteurs[] = $codeProd;
        continue;
      }

      if ($row['codeparcelle']) {
        $codeParc = trim((string) $row['codeparcelle']);
      } else {
        $codeProd = $verification->codeProdapp;
        $codeParc = $this->generecodeparc($verification->id, $codeProd);
      }

      $superficie = $row['superficie'];
      $superficie = Str::before($superficie, ' ');
      if (Str::contains($superficie, ',')) {
        $superficie = Str::replaceFirst(',', '.', $superficie);
        if (Str::contains($superficie, ',')) {
          $superficie = Str::replaceFirst('m2', '', $superficie);
        }
      }

      $latitude = is_numeric($row['latitude']) ? round($row['latitude'], 6) : null;
      $longitude = is_numeric($row['longitude']) ? round($row['longitude'], 6) : null;

      $data = [
        'producteur_id' => $verification->id,
        'codeParc' => $codeParc,
        'anneeCreation' => $row['anneecreation'],
        'typedeclaration' => 'Verbale',
        'culture' => $row['cultureparcelle'],
        'superficie' => is_numeric(trim($superficie)) ? round(trim($superficie), 2) : trim($superficie),
        'latitude' => $latitude,
        'longitude' => $longitude,
        'userid' => auth()->user()->id,
        'updated_at' => now(),
      ];

      $parcelle = Parcelle::where('producteur_id', $verification->id)
        ->where('codeParc', $codeParc)
        ->first();

      if ($parcelle) {
        $parcelle->update($data);
        $this->updated++;
      } else {
        $data['created_at'] = now();
        DB::table('parcelles')->insert($data);
        $this->created++;
      }
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
