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

    public function rules(): array
    {
        return [
            'superficie'      => 'required',
            'codeproducteur'  => 'required',
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
                ->where('codeProd', $codeProd)
                ->orWhere('codeProdapp', $codeProd)
                ->first();

            if ($verification == null) {
                $this->missingProducteurs[] = $codeProd;
                continue;
            }

            // Normalize superficie
            $superficie = Str::before((string) $row['superficie'], ' ');
            if (Str::contains($superficie, ',')) {
                $superficie = Str::replaceFirst(',', '.', $superficie);
                if (Str::contains($superficie, ',')) {
                    $superficie = Str::replaceFirst('m2', '', $superficie);
                }
            }

            $latitude  = is_numeric($row['latitude'])  ? round($row['latitude'],  6) : null;
            $longitude = is_numeric($row['longitude']) ? round($row['longitude'], 6) : null;

            // Find existing parcelle and determine codeParc
            $parcelle = null;

            if (!empty(trim((string) ($row['codeparcelle'] ?? '')))) {
                // Code fourni dans le fichier : recherche par producteur_id + codeParc
                $codeParc = trim((string) $row['codeparcelle']);
                $parcelle = Parcelle::where('producteur_id', $verification->id)
                    ->where('codeParc', $codeParc)
                    ->first();
            } else {
                // Pas de code dans le fichier : recherche par producteur_id + coordonnées GPS
                if ($latitude && $longitude) {
                    $parcelle = Parcelle::where('producteur_id', $verification->id)
                        ->where('latitude', $latitude)
                        ->where('longitude', $longitude)
                        ->first();
                }

                if ($parcelle) {
                    $codeParc = $parcelle->codeParc;
                } else {
                    // Aucune correspondance : génération d'un nouveau code
                    $codeParc = $this->generecodeparc(
                        $verification->id,
                        $verification->codeProdapp ?? $verification->codeProd
                    );
                }
            }

            $data = [
                'producteur_id'   => $verification->id,
                'codeParc'        => $codeParc,
                'anneeCreation'   => $row['anneecreation'] ?? null,
                'typedeclaration' => 'Verbale',
                'culture'         => $row['cultureparcelle'] ?? null,
                'superficie'      => is_numeric(trim($superficie)) ? round(trim($superficie), 2) : trim($superficie),
                'latitude'        => $latitude,
                'longitude'       => $longitude,
                'userid'          => auth()->user()->id,
                'updated_at'      => now(),
            ];

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
        if (!$codeProd) {
            return '';
        }

        $action = 'non';

        $data = Parcelle::select('codeParc')->where([
            ['producteur_id', $idProd],
            ['codeParc', '!=', null],
        ])->orderby('id', 'desc')->first();

        if ($data) {
            $code   = $data->codeParc;
            $numero = $code ? ((int) Str::after(Str::afterLast($code, '-'), 'P')) + 1 : 1;
        } else {
            $numero = 1;
        }

        $codeParc = $codeProd . '-P' . $numero;

        do {
            $verif = Parcelle::select('codeParc')->where('codeParc', $codeParc)->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action   = 'oui';
                $numero++;
                $codeParc = $codeProd . '-P' . $numero;
            }
        } while ($action !== 'non');

        return $codeParc;
    }
}
