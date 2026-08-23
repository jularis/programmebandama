<?php

namespace App\Imports;

use App\Models\Parcelle;
use App\Models\Producteur;
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
            $codeProd = trim((string) $this->value($row, ['codeproducteur', 'code_producteur']));

            $verification = Producteur::joinRelationship('localite.section')
                ->where('cooperative_id', auth()->user()->cooperative_id)
                ->where(function ($query) use ($codeProd) {
                    $query->where('producteurs.codeProd', $codeProd)
                        ->orWhere('producteurs.codeProdapp', $codeProd);
                })
                ->select('producteurs.*')
                ->first();

            if ($verification == null) {
                $this->missingProducteurs[] = $codeProd;
                continue;
            }

            // Normalize superficie
            $superficie = Str::before((string) $this->value($row, ['superficie']), ' ');
            if (Str::contains($superficie, ',')) {
                $superficie = Str::replaceFirst(',', '.', $superficie);
                if (Str::contains($superficie, ',')) {
                    $superficie = Str::replaceFirst('m2', '', $superficie);
                }
            }

            $latitudeValue  = $this->value($row, ['latitude']);
            $longitudeValue = $this->value($row, ['longitude']);
            $latitude  = is_numeric($latitudeValue)  ? round($latitudeValue,  6) : null;
            $longitude = is_numeric($longitudeValue) ? round($longitudeValue, 6) : null;

            // Find existing parcelle and determine codeParc
            $parcelle = null;

            if (!empty(trim((string) $this->value($row, ['codeparcelle', 'code_parcelle'])))) {
                // Code fourni dans le fichier : recherche par producteur_id + codeParc
                $codeParc = trim((string) $this->value($row, ['codeparcelle', 'code_parcelle']));
                $parcelle = Parcelle::joinRelationship('producteur.localite.section')
                    ->where('cooperative_id', auth()->user()->cooperative_id)
                    ->where('codeParc', $codeParc)
                    ->select('parcelles.*')
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
                'anneeCreation'   => $this->value($row, ['anneecreation', 'annee_creation']),
                'typedeclaration' => $this->value($row, ['typedeclaration', 'type_declaration']) ?: ($parcelle->typedeclaration ?? 'Verbale'),
                'culture'         => $this->value($row, ['cultureparcelle', 'culture_parcelle', 'culture']),
                'superficie'      => is_numeric(trim($superficie)) ? round(trim($superficie), 2) : trim($superficie),
                'latitude'        => $latitude,
                'longitude'       => $longitude,
                'userid'          => auth()->user()->id,
                'updated_at'      => now(),
            ];

            if ($parcelle) {
                $parcelle->update(array_filter($data, fn($value) => $value !== null));
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

    private function value($row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && trim((string) $row[$key]) !== '') {
                return is_string($row[$key]) ? trim($row[$key]) : $row[$key];
            }
        }

        return null;
    }
}
